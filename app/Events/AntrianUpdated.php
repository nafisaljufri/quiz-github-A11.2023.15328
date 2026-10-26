<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $idJadwal,
        public ?int $nomor,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('antrian')];
    }

    public function broadcastAs(): string
    {
        return 'antrian.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id_jadwal' => $this->idJadwal,
            'nomor' => $this->nomor,
        ];
    }
}