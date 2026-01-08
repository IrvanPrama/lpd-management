<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitLoss extends Model
{
    protected $table = 'profit_losses';
    protected $fillable =
        [
            'tahun',
            'bulan',
            'tanggal',
            'jenis',
            'sumber',
            'total_pendapatan',
            'total_pengeluaran',
        ];
}
