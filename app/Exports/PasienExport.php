<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PasienExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $textColumns = ['C', 'D', 'E']; // No. RM, No. KTP, No. HP

    public function collection()
    {
        return User::where('role', 'pasien')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'No. RM', 'No. KTP', 'No. HP', 'Alamat', 'Email'];
    }

    public function map($pasien): array
    {
        return [
            ++$this->no,
            $pasien->nama,
            $pasien->no_rm,
            $pasien->no_ktp,
            $pasien->no_hp,
            $pasien->alamat,
            $pasien->email,
        ];
    }
}