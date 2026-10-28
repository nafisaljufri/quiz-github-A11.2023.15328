<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class PembayaranLunasNotification extends Notification
{
    public function __construct(public Periksa $periksa) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $tanggal = Carbon::parse($this->periksa->tgl_periksa)->format('d M Y');
        $total = number_format($this->periksa->biaya_periksa, 0, ',', '.');

        return [
            'pesan' => "Pembayaran Anda untuk pemeriksaan tanggal {$tanggal} (Rp {$total}) telah diverifikasi dan dinyatakan LUNAS.",
            'url' => route('pasien.pembayaran.show', $this->periksa->id, false),
            'id_periksa' => $this->periksa->id,
        ];
    }
}