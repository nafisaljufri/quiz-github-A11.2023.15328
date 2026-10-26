<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class JadwalPeriksa extends Model
{
    protected $table = 'jadwal_periksa';

    protected $fillable = [
        'id_dokter',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }

    public function daftarPolis()
    {
        return $this->hasMany(DaftarPoli::class, 'id_jadwal');
    }

    public function tanggalBerikutnya(): Carbon
    {
        $map = [
            'Senin' => Carbon::MONDAY, 'Selasa' => Carbon::TUESDAY, 'Rabu' => Carbon::WEDNESDAY,
            'Kamis' => Carbon::THURSDAY, 'Jumat' => Carbon::FRIDAY, 'Sabtu' => Carbon::SATURDAY,
            'Minggu' => Carbon::SUNDAY,
        ];

        $hariIni = Carbon::today();

        return $hariIni->dayOfWeek === $map[$this->hari]
            ? $hariIni
            : $hariIni->copy()->next($map[$this->hari]);
    }
}