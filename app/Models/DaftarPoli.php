<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DaftarPoli extends Model
{
    protected $table = 'daftar_poli';

    protected $fillable = [
        'id_pasien',
        'id_jadwal',
        'tanggal',
        'keluhan',
        'no_antrian'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'id_pasien');
    }

    public function jadwalPeriksa()
    {
        return $this->belongsTo(JadwalPeriksa::class, 'id_jadwal');
    }

    public function periksas()
    {
        return $this->hasMany(Periksa::class, 'id_daftar_poli');
    }

    public function periksa()
    {
        return $this->hasOne(Periksa::class, 'id_daftar_poli');
    }

    public static function nomorSedangDilayani(): Collection
    {
        return static::aktif()
            ->selectRaw('id_jadwal, MIN(no_antrian) as nomor')
            ->groupBy('id_jadwal')
            ->pluck('nomor', 'id_jadwal');
    }

    public static function nomorUntukJadwal(int $idJadwal): ?int
    {
        return static::aktif()
            ->where('id_jadwal', $idJadwal)
            ->min('no_antrian');
    }

    public function scopeAktif($query)
    {
        return $query->whereDoesntHave('periksas')
            ->whereDate('tanggal', '>=', today());
    }
}
