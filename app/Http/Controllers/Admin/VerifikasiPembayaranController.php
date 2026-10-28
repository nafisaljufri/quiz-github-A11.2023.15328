<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\PembayaranLunasNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiPembayaranController extends Controller
{
    private const STATUS = ['menunggu_verifikasi', 'lunas', 'belum_bayar'];

    public function index()
    {
        $status = in_array(request('status'), self::STATUS, true)
            ? request('status')
            : 'menunggu_verifikasi';

        $tagihans = Periksa::with(['daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter.poli'])
            ->where('status_bayar', $status)
            ->latest('tgl_periksa')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pembayaran.index', compact('tagihans', 'status'));
    }

    public function show($id)
    {
        $periksa = Periksa::with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat',
        ])->findOrFail($id);

        return view('admin.pembayaran.show', compact('periksa'));
    }

    public function konfirmasi($id)
    {
        $periksa = DB::transaction(function () use ($id) {
            $periksa = Periksa::with('daftarPoli.pasien')->lockForUpdate()->findOrFail($id);

            // Hanya tagihan yang menunggu verifikasi yang boleh dikonfirmasi
            if ($periksa->status_bayar !== 'menunggu_verifikasi') {
                return null;
            }

            $periksa->update([
                'status_bayar' => 'lunas',
                'tgl_lunas' => now(),
            ]);

            return $periksa;
        });

        if (! $periksa) {
            return redirect()->route('admin.pembayaran.index')
                ->with('error', 'Tagihan ini tidak sedang menunggu verifikasi.');
        }

        try {
            $periksa->daftarPoli->pasien?->notify(new PembayaranLunasNotification($periksa));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim notifikasi pembayaran lunas.', [
                'periksa_id' => $periksa->id,
                'exception' => $e,
            ]);
        }

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi sebagai lunas.');
    }
}
