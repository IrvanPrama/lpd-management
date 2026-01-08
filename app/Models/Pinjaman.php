<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pinjaman extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'anggota_id',
        'anggota_name',
        'jumlah_pinjaman',
        'tenor',
        'jenis_bunga',
        'bunga',
        'tanggal_pinjaman',
        'tanggal_disetujui',
        'jatuh_tempo',
        'bukti',
        'status',
    ];

    // Pinjaman.php
    public function angsurans()
    {
        return $this->hasMany(Angsuran::class);
    }
}
