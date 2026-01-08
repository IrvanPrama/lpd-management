<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equities extends Model
{
    protected $table = 'equities';
    protected $fillable = ['tanggal', 'jenis', 'sub_jenis', 'nama', 'jumlah', 'status'];
}
