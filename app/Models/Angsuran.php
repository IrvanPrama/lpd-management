<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Angsuran extends Model
{
    use HasFactory;
    use Notifiable;
    protected $fillable = [
        'pinjaman_id',
        'nik',
        'anggota_name',
        'angsuran_ke',
        'jumlah_angsuran',
        'jenis_bunga',
        'bunga_angsuran',
        'tenggat_waktu',
        'tanggal_bayar',
        'jenis',
        'bukti',
        'status',
        'is_didenda',
    ];
}
