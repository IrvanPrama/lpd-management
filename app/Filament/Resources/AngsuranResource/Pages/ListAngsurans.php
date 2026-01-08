<?php

namespace App\Filament\Resources\AngsuranResource\Pages;

use App\Filament\Resources\AngsuranResource;
use App\Models\Angsuran;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListAngsurans extends ListRecords
{
    protected static string $resource = AngsuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cekDenda')
                ->label('Cek & Tambah Denda')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->requiresConfirmation()
                ->action(function () {
                    DB::transaction(function () {
                        $angsurans = Angsuran::where('status', 'belum_bayar')
                            ->whereDate('tenggat_waktu', '<', now()->subDay())
                            ->where('is_didenda', false)
                            ->get();

                        foreach ($angsurans as $a) {
                            // buat denda baru
                            Angsuran::create([
                                'pinjaman_id' => $a->pinjaman_id,
                                'anggota_name' => $a->anggota_name,
                                'angsuran_ke' => $a->angsuran_ke,
                                'jumlah_angsuran' => 0,
                                'bunga_angsuran' => $a->bunga_angsuran, // 1x bunga
                                'jenis' => 'denda',
                                'status' => 'belum_bayar',
                                'is_didenda' => true,
                                'tenggat_waktu' => Carbon::now()->addDays(7), // bebas, contoh 7 hari
                            ]);

                            // tandai sudah kena denda
                            $a->is_didenda = true;
                            $a->save();
                        }
                    });

                    Notification::make()
                        ->title('Denda berhasil dibuat untuk angsuran yang terlambat')
                        ->success()
                        ->send();
                }),
        ];
    }
}
