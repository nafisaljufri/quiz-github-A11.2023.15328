<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DokterExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $textColumns = ['D', 'E']; // No. KTP, No. HP

    public function collection()
    {
        return User::where('role', 'dokter')->with('poli')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'Email', 'No. KTP', 'No. HP', 'Alamat', 'Poli'];
    }

    public function map($dokter): array
    {
        return [
            ++$this->no,
            $dokter->nama,
            $dokter->email,
            $dokter->no_ktp,
            $dokter->no_hp,
            $dokter->alamat,
            $dokter->poli->nama_poli ?? '-',
        ];
    }
}