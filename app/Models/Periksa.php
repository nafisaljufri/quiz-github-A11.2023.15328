<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periksa extends Model
{
    protected $table = 'periksa';

    protected $fillable = [
        'id_daftar_poli',
        'tgl_periksa',
        'catatan',
        'biaya_periksa',
        'status_bayar',
        'bukti_bayar',
        'tgl_upload_bukti',
        'tgl_lunas',
    ];

    protected $casts = [
        'tgl_upload_bukti' => 'datetime',
        'tgl_lunas' => 'datetime',
    ];

    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_periksa');
    }

    // URL foto bukti (null jika belum upload)
    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_bayar ? asset('storage/' . $this->bukti_bayar) : null;
    }
}