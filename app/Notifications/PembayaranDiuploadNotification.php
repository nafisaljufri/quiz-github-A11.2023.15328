<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Notifications\Notification;

class PembayaranDiuploadNotification extends Notification
{
    public function __construct(public Periksa $periksa) {}

    // Hanya disimpan ke tabel notifications (database channel)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $pasien = $this->periksa->daftarPoli->pasien->nama ?? 'Pasien';

        return [
            'pesan' => "{$pasien} telah mengupload bukti pembayaran untuk pemeriksaan yang Anda lakukan.",
            'url' => route('riwayat-pasien.show', $this->periksa->id, false),
            'id_periksa' => $this->periksa->id,
        ];
    }
}