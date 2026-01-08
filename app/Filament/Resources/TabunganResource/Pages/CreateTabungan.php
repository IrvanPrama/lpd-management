<?php

namespace App\Filament\Resources\TabunganResource\Pages;

use App\Filament\Resources\TabunganResource;
use App\Models\Equities;
use Filament\Resources\Pages\CreateRecord;

class CreateTabungan extends CreateRecord
{
    protected static string $resource = TabunganResource::class;

    protected function afterCreate(): void
    {
        $tabungan = $this->record;

        Equities::create([
            'anggota_id' => $tabungan->anggota_id,
            'nama' => $tabungan->anggota_name,
            'sumber' => 'Tabungan',
            'jenis' => 'Tabungan',
            'sub_jenis' => 'Tabungan',
            'keterangan' => $tabungan->jenis_tabungan,
            'jumlah' => $tabungan->jumlah_setoran,
            'tanggal' => $tabungan->tanggal_setor,
            'status' => 'masuk',
        ]);
    }
}
