<?php

namespace App\Http\Controllers\Dokter;

use App\Exports\JadwalPeriksaExport;
use App\Exports\RiwayatPasienExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportDokterController extends Controller
{
    public function jadwalPeriksa()
    {
        return Excel::download(
            new JadwalPeriksaExport(Auth::id()),
            'jadwal-periksa-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function riwayatPasien()
    {
        return Excel::download(
            new RiwayatPasienExport(Auth::id()),
            'riwayat-pasien-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}