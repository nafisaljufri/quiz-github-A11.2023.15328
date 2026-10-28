<?php

namespace App\Exports;

use App\Models\Periksa;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiwayatPasienExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected array $textColumns = ['D']; // No. RM

    public function __construct(private int $idDokter) {}

    public function collection()
    {
        // Eager loading: jumlah query tetap berapa pun jumlah barisnya
        return Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) {
                $query->where('id_dokter', $this->idDokter);
            })
            ->with(['daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter.poli', 'detailPeriksas.obat'])
            ->orderByDesc('tgl_periksa')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal Periksa', 'Nama Pasien', 'No. RM', 'Poli', 'No. Antrian',
            'Keluhan', 'Catatan Dokter', 'Obat', 'Total Biaya (Rp)', 'Status Pembayaran',
        ];
    }

    public function map($periksa): array
    {
        $daftar = $periksa->daftarPoli;

        $status = match ($periksa->status_bayar) {
            'lunas' => 'Lunas',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            default => 'Belum Dibayar',
        };

        return [
            ++$this->no,
            Carbon::parse($periksa->tgl_periksa)->format('d-m-Y H:i'),
            $daftar->pasien->nama ?? '-',
            $daftar->pasien->no_rm ?? '-',
            $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-',
            $daftar->no_antrian,
            $daftar->keluhan ?: '-',
            $periksa->catatan ?: '-',
            $periksa->detailPeriksas->map(fn ($d) => $d->obat->nama_obat ?? '-')->implode(', ') ?: '-',
            $periksa->biaya_periksa,
            $status,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => '#,##0',
        ];
    }
}