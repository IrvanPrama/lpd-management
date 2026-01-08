<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    protected $table = 'assets';
    protected $fillable = [
        'tanggal',
        'jenis',
        'sub_jenis',
        'nama',
        'jumlah',
        'status',
    ];
}
