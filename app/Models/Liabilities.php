<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liabilities extends Model
{
    protected $table = 'liabilities';
    protected $fillable = ['tanggal', 'jenis', 'sub_jenis', 'name', 'jumlah', 'status'];
}
