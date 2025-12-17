<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pemasukan extends Model
{
    use HasFactory;
    use Notifiable;
    protected $fillable = [
        'sumber_pemasukan',
        'jumlah_pemasukan',
        'tanggal_pemasukan',
        'keterangan',
    ];
}
