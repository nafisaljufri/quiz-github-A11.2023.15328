<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ObatExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    public function collection()
    {
        return Obat::orderBy('nama_obat')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Obat', 'Kemasan', 'Harga (Rp)', 'Stok'];
    }

    public function map($obat): array
    {
        return [
            ++$this->no,
            $obat->nama_obat,
            $obat->kemasan ?: '-',
            $obat->harga,   // tetap angka agar bisa dijumlah di Excel
            $obat->stok,
        ];
    }

    // Kolom D (harga) diberi pemisah ribuan
    public function columnFormats(): array
    {
        return [
            'D' => '#,##0',
        ];
    }
}