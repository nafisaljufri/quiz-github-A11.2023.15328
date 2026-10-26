<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        // Eager loading: jumlah query tetap, berapa pun jumlah barisnya
        $riwayats = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'periksa'])
            ->where('id_pasien', Auth::id())
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pasien.riwayat.index', compact('riwayats'));
    }

    public function show($id)
    {
        // where id_pasien => pasien hanya bisa membuka riwayat miliknya sendiri
        $daftar = DaftarPoli::with([
                'jadwalPeriksa.dokter.poli',
                'periksa.detailPeriksas.obat',
            ])
            ->where('id_pasien', Auth::id())
            ->findOrFail($id);

        if (! $daftar->periksa) {
            return redirect()->route('pasien.riwayat')
                ->with('error', 'Pemeriksaan untuk pendaftaran ini belum dilakukan.');
        }

        return view('pasien.riwayat.show', compact('daftar'));
    }
}