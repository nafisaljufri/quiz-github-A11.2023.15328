<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $antrianAktif = DaftarPoli::aktif()
            ->with('jadwalPeriksa.dokter.poli')
            ->where('id_pasien', Auth::id())
            ->latest()
            ->first();

        $jadwals = JadwalPeriksa::with('dokter.poli')
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 WHEN 'Minggu' THEN 7 END")
            ->orderBy('jam_mulai')
            ->get();

        $sedangDilayani = DaftarPoli::nomorSedangDilayani();

        return view('pasien.dashboard', compact('antrianAktif', 'jadwals', 'sedangDilayani'));
    }
}
