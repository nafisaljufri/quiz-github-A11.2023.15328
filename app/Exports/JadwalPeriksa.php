<?php

namespace App\Exports;

use App\Models\JadwalPeriksa;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JadwalPeriksaExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    // id dokter dikirim dari controller, bukan dari input pengguna
    public function __construct(private int $idDokter) {}

    public function collection()
    {
        return JadwalPeriksa::where('id_dokter', $this->idDokter)
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 WHEN 'Minggu' THEN 7 END")
            ->orderBy('jam_mulai')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Hari', 'Jam Mulai', 'Jam Selesai'];
    }

    public function map($jadwal): array
    {
        return [
            ++$this->no,
            $jadwal->hari,
            Carbon::parse($jadwal->jam_mulai)->format('H:i'),
            Carbon::parse($jadwal->jam_selesai)->format('H:i'),
        ];
    }
}