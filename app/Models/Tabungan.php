<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    protected $fillable = [
        'anggota_id',
        'nik',
        'anggota_name',
        'jumlah_setoran',
        'tanggal_setor',
        'bukti',
    ];
}
