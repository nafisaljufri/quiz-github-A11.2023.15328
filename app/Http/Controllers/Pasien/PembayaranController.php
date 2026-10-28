<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\PembayaranDiuploadNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $tagihans = $this->milikPasien()
            ->with('daftarPoli.jadwalPeriksa.dokter.poli')
            ->latest('tgl_periksa')
            ->paginate(10);

        return view('pasien.pembayaran.index', compact('tagihans'));
    }

    public function show($id)
    {
        $periksa = $this->milikPasien()
            ->with(['daftarPoli.jadwalPeriksa.dokter.poli', 'detailPeriksas.obat'])
            ->findOrFail($id);

        return view('pasien.pembayaran.show', compact('periksa'));
    }

    public function upload(Request $request, $id)
    {
        $periksa = $this->milikPasien()->findOrFail($id);

        if ($periksa->status_bayar === 'lunas') {
            return redirect()->route('pasien.pembayaran.show', $periksa->id)
                ->with('error', 'Tagihan ini sudah lunas.');
        }

        if ($periksa->status_bayar === 'menunggu_verifikasi') {
            return redirect()->route('pasien.pembayaran.show', $periksa->id)
                ->with('error', 'Bukti pembayaran sedang menunggu verifikasi.');
        }

        $request->validate([
            'bukti_bayar' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'bukti_bayar.required' => 'Foto bukti pembayaran wajib diupload.',
            'bukti_bayar.image' => 'File harus berupa gambar.',
            'bukti_bayar.mimes' => 'Format file harus JPG, JPEG, atau PNG.',
            'bukti_bayar.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        // Jika upload ulang, hapus file lama agar tidak menumpuk
        if ($periksa->bukti_bayar) {
            Storage::disk('public')->delete($periksa->bukti_bayar);
        }

        $path = $request->file('bukti_bayar')->store('bukti-bayar', 'public');

        $periksa->update([
            'bukti_bayar' => $path,
            'status_bayar' => 'menunggu_verifikasi',
            'tgl_upload_bukti' => now(),
        ]);

        $periksa->load('daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter');
        $dokter = $periksa->daftarPoli->jadwalPeriksa->dokter ?? null;

        if ($dokter) {
            try {
                $dokter->notify(new PembayaranDiuploadNotification($periksa));
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim notifikasi bukti pembayaran.', [
                    'periksa_id' => $periksa->id,
                    'exception' => $e,
                ]);
            }
        }

        return redirect()->route('pasien.pembayaran.show', $periksa->id)
            ->with('success', 'Bukti pembayaran berhasil diupload dan menunggu verifikasi.');
    }

    // Hanya pemeriksaan milik pasien yang sedang login
    private function milikPasien()
    {
        return Periksa::whereHas('daftarPoli', function ($query) {
            $query->where('id_pasien', Auth::id());
        });
    }
}
