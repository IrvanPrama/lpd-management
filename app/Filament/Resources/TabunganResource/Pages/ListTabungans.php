<?php

namespace App\Filament\Resources\TabunganResource\Pages;

use App\Filament\Resources\TabunganResource;
use App\Models\Bunga;
use App\Models\Nasabah;
use App\Models\Tabungan;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListTabungans extends ListRecords
{
    protected static string $resource = TabunganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('tambah_bunga')
                ->label('Bunga Tabungan')
                ->color('success')
                ->icon('heroicon-o-plus-circle')
                ->requiresConfirmation()
                ->action(function () {
                    DB::transaction(function () {
                        $total_nasabah = Nasabah::count();

                        for ($id = 1; $id <= ($total_nasabah + 20); ++$id) {
                            if (!Nasabah::all()->where('id', $id)->first()) {
                                continue;
                            }
                            $nik_per_nasabah = Nasabah::all()->where('id', $id)->first()->nik;
                            $total = Tabungan::where('nik', $nik_per_nasabah)
                                // ->where('jenis_tabungan', '!=', 'bunga')
                                ->sum('jumlah_setoran');

                            $bunga = $total * (Bunga::where('nama_bunga', 'tabungan')->first()->persentase_bunga / 100);

                            // buat denda baru
                            Tabungan::create([
                                'anggota_id' => $id,
                                'nik' => $nik_per_nasabah,
                                'anggota_name' => Nasabah::all()->where('id', $id)->first()->name,
                                'jumlah_setoran' => round($bunga, 2),
                                'tanggal_setor' => Carbon::now()->toDateString(),
                                'bukti' => 'Bunga dari sistem',
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Bunga berhasil dibuat untuk semua nasabah')
                        ->success()
                        ->send();
                }),
        ];
    }
}
