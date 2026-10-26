<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PeriksaPasienController extends Controller
{
    private const BIAYA_JASA_PERIKSA = 150000;

    public function index()
    {
        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) {
                $query->where('id_dokter', Auth::id());
            })
            ->orderBy('id_jadwal')
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        $daftarPasien = $this->antreanDokter()->with(['pasien', 'jadwalPeriksa'])->findOrFail($id);

        if ($daftarPasien->periksas()->exists()) {
            return redirect()->route('periksa-pasien.index')
                ->with('error', 'Antrean ini sudah diperiksa.');
        }

        $obats = Obat::orderBy('nama_obat')->get();

        return view('dokter.periksa-pasien.create', compact('obats', 'daftarPasien'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_daftar_poli' => ['required', 'integer'],
            'obat_json' => ['required', 'json'],
            'catatan' => ['nullable', 'string'],
        ]);

        $daftarPasien = $this->antreanDokter()->findOrFail($validated['id_daftar_poli']);
        $obatIds = json_decode($validated['obat_json'], true);

        Validator::make(['obat_ids' => $obatIds], [
            'obat_ids' => ['required', 'array', 'min:1'],
            'obat_ids.*' => ['required', 'integer', 'distinct', 'exists:obat,id'],
        ])->validate();

        if ($daftarPasien->periksas()->exists()) {
            return redirect()->route('periksa-pasien.index')
                ->with('error', 'Antrean ini sudah diperiksa.');
        }

        DB::transaction(function () use ($daftarPasien, $obatIds, $validated) {
            $obats = Obat::whereIn('id', $obatIds)->get();
            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPasien->id,
                'tgl_periksa' => now(),
                'catatan' => $validated['catatan'] ?? null,
                'biaya_periksa' => $obats->sum('harga') + self::BIAYA_JASA_PERIKSA,
            ]);

            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);
            }
        });

        return redirect()->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }

    private function antreanDokter()
    {
        return DaftarPoli::whereHas('jadwalPeriksa', function ($query) {
            $query->where('id_dokter', Auth::id());
        });
    }
}
