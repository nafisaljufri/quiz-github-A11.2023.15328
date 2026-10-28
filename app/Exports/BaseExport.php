<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExport extends DefaultValueBinder implements ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    /** Huruf kolom yang harus tetap berupa teks (mis. No. KTP, No. HP) */
    protected array $textColumns = [];

    /** Penomor baris */
    protected int $no = 0;

    // Paksa kolom tertentu menjadi teks agar angka panjang tidak berubah
    // menjadi 3,2E+15 dan angka 0 di depan tidak hilang
    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), $this->textColumns, true) && $value !== null && $value !== '') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    // Baris pertama (header) dicetak tebal
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}