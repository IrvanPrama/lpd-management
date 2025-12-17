<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AngsuranResource\Pages;
use App\Models\Angsuran;
use App\Models\Pemasukan;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AngsuranResource extends Resource
{
    protected static ?string $model = Angsuran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pinjaman_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('angsuran_ke')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('jumlah_angsuran')
                    ->numeric(),
                Forms\Components\TextInput::make('bunga_angsuran')
                    ->numeric(),
                DatePicker::make('tenggat_waktu')
                    ->required(),
                DatePicker::make('tanggal_bayar')
                    ->default(now()),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('belum_bayar'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pinjaman_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('anggota_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('angsuran_ke')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_angsuran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bunga_angsuran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenggat_waktu')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                  ->searchable(),
                Tables\Columns\TextColumn::make('bukti')
                    ->label('Bukti')
                    ->url(fn ($record) => Storage::url($record->bukti))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
           ->actions([
               Action::make('bayar')
                   ->label('Bayar')
                   ->icon('heroicon-o-banknotes')
                   ->color('success')
                   ->visible(fn (Angsuran $record) => $record->status === 'belum_bayar')
                   ->modalHeading('Pembayaran Angsuran')
                   ->modalSubmitActionLabel('Bayar')
                   ->form([
                       DatePicker::make('tanggal_bayar')
                           ->label('Tanggal Bayar')
                           ->required()
                           ->default(now()),

                       FileUpload::make('bukti')
                           ->label('Bukti Pembayaran')
                           ->image()
                           ->disk('public')
                           ->directory('bukti-angsuran')
                           ->required(),
                   ])
                   ->action(function (Angsuran $record, array $data) {
                       DB::transaction(function () use ($record, $data) {
                           // 1️⃣ Update angsuran
                           $record->update([
                               'tanggal_bayar' => $data['tanggal_bayar'],
                               'bukti' => $data['bukti'],
                               'status' => 'sudah_bayar',
                           ]);

                           // 2️⃣ Hitung total pemasukan
                           $totalPemasukan = $record->jumlah_angsuran + $record->bunga_angsuran;

                           // 3️⃣ Simpan ke tabel pemasukan
                           Pemasukan::create([
                               'sumber_pemasukan' => $record->jenis.'-pinjaman #'.$record->pinjaman_id.
                                                      ' - Angsuran ke '.$record->angsuran_ke,
                               'jumlah_pemasukan' => $totalPemasukan,
                               'tanggal_pemasukan' => $record->tanggal_bayar,
                               'keterangan' => 'Pembayaran angsuran pinjaman',
                           ]);
                       });
                   })
                   ->requiresConfirmation(),

               Tables\Actions\EditAction::make(),
           ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAngsurans::route('/'),
            'create' => Pages\CreateAngsuran::route('/create'),
            'edit' => Pages\EditAngsuran::route('/{record}/edit'),
        ];
    }
}
