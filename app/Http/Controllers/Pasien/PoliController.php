<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Events\AntrianUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwal,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string',
        ]);

        $pasienId = Auth::id();

        $punyaAntrianAktif = DaftarPoli::aktif()
            ->where('id_pasien', $pasienId)
            ->exists();

        if ($punyaAntrianAktif) {
            return redirect()->back()->withInput()->withErrors([
                'antrian' => 'Anda masih memiliki antrian aktif. Anda baru dapat mendaftar lagi setelah pemeriksaan selesai.',
            ]);
        }

        $jadwal = JadwalPeriksa::with('dokter')->findOrFail($request->id_jadwal);

        if ((int) optional($jadwal->dokter)->id_poli !== (int) $request->id_poli) {
            return redirect()->back()->withInput()->withErrors([
                'id_jadwal' => 'Jadwal yang dipilih tidak sesuai dengan poli.',
            ]);
        }

        $tanggal = $jadwal->tanggalBerikutnya()->toDateString();

        DB::transaction(function () use ($request, $pasienId, $tanggal) {
            $terakhir = DaftarPoli::where('id_jadwal', $request->id_jadwal)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->max('no_antrian');

            DaftarPoli::create([
                'id_pasien' => $pasienId,
                'id_jadwal' => $request->id_jadwal,
                'tanggal' => $tanggal,
                'keluhan' => $request->keluhan ?? '',
                'no_antrian' => ($terakhir ?? 0) + 1,
            ]);
        });

        try {
            AntrianUpdated::dispatch(
                (int) $request->id_jadwal,
                DaftarPoli::nomorUntukJadwal((int) $request->id_jadwal)
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('pasien.dashboard')
            ->with('success', 'Berhasil mendaftar ke poli.');
    }
}
