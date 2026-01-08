<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PinjamanResource\Pages;
use App\Models\Angsuran;
use App\Models\Bunga;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PinjamanResource extends Resource
{
    protected static ?string $model = Pinjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('anggota_id')
                    ->label('Nasabah')
                    ->options([
                        Nasabah::query()->where('status', 'available')
                            ->pluck('name', 'id')
                            ->toArray(),
                    ])
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('anggota_name', Nasabah::find($get('anggota_id'))->name);
                    }),

                Forms\Components\TextInput::make('anggota_name')
                    ->label('Nama Nasabah')
                    ->disabled()
                    ->dehydrated(),

                Select::make('jenis_bunga')
                    ->label('Jenis Bunga')
                    ->options([
                        'flat' => 'Flat',
                        'menurun' => 'Menurun',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('jumlah_pinjaman')
                    ->label('Jumlah Pinjaman')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $jumlah = $get('jumlah_pinjaman');
                        $tenor = $get('tenor');

                        if (!$jumlah || !$tenor) {
                            $set('bunga', 0);

                            return;
                        }

                        $persentase = Bunga::value('persentase_bunga') ?? 0;

                        $totalBunga = ($jumlah * ($persentase / 100)) * $tenor;

                        $set('bunga', round($totalBunga, 2));
                    }),

                Forms\Components\TextInput::make('tenor')
                    ->label('Tenor (bulan)')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // hitung bunga
                        $jumlah = $get('jumlah_pinjaman');
                        $tenor = $get('tenor');

                        if ($jumlah && $tenor) {
                            $persentase = Bunga::value('persentase_bunga') ?? 0;
                            $set('bunga', round(($jumlah * $persentase / 100) * $tenor, 2));
                        }

                        // hitung jatuh tempo
                        self::hitungJatuhTempo($get, $set);
                    }),

                Forms\Components\TextInput::make('bunga')
                     ->label('Total Bunga')
                     ->numeric()
                     ->disabled()
                     ->dehydrated(), // tetap tersimpan ke DB

                DatePicker::make('tanggal_pinjaman')
                    ->label('Tanggal Pinjaman')
                    ->live()
                    ->afterStateUpdated(
                        fn (Get $get, Set $set) => self::hitungJatuhTempo($get, $set)
                    ),

                DatePicker::make('tanggal_disetujui'),

                DatePicker::make('jatuh_tempo')
                    ->label('Jatuh Tempo'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'running' => 'Running',
                        'rejected' => 'Rejected',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anggota_id')
                    ->label('Nasabah ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('anggota_name')
                    ->label('Nama Nasabah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tenor')
                    ->label('Tenor (bulan)')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_pinjaman')
                    ->label('Jumlah Pinjaman')
                    ->prefix('Rp ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pinjaman')
                    ->label('Tanggal Pinjaman')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bunga')
                    ->label('Bunga')
                    ->prefix('Rp ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bukti')
                    ->label('Bukti')
                    ->url(fn ($record) => Storage::url($record->bukti))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Pinjaman $record) => $record->status === 'pending')
                    ->modalHeading('Persetujuan Pinjaman')
                    ->modalSubmitActionLabel('Setujui')
                    ->form([
                        DatePicker::make('tanggal_disetujui')
                            ->label('Tanggal Disetujui')
                            ->required()
                            ->default(now()),

                        FileUpload::make('bukti')
                            ->label('Bukti Persetujuan')
                            ->image()
                            ->disk('public')
                            ->directory('bukti-disetujui')
                            ->required(),
                    ])
                    ->action(function (Pinjaman $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            // CEGAH DOUBLE APPROVE
                            if ($record->status !== 'pending') {
                                return;
                            }

                            $tanggalPinjaman = Carbon::parse($data['tanggal_disetujui']);

                            // 1️ Update pinjaman
                            $record->update([
                                'tanggal_disetujui' => $data['tanggal_disetujui'],
                                'tanggal_pinjaman' => $tanggalPinjaman->toDateString(),
                                'jatuh_tempo' => $tanggalPinjaman
                                    ->copy()
                                    ->addMonthsNoOverflow((int) $record->tenor)
                                    ->toDateString(),
                                'bukti' => $data['bukti'],
                                'status' => 'running',
                            ]);

                            // 2️ Generate angsuran
                            $tenor = (int) $record->tenor;

                            // Ambil persen bunga
                            $persentase = (Bunga::value('persentase_bunga') ?? 0) / 100;

                            if ($record->jenis_bunga === 'flat') {
                                // JENIS BUNGA FLAT
                                $angsuranPokok = $record->jumlah_pinjaman / $tenor;
                                $angsuranBunga = ($record->jumlah_pinjaman * $persentase);

                                for ($i = 1; $i <= $tenor; ++$i) {
                                    $tenggatWaktu = $tanggalPinjaman
                                        ->copy()
                                        ->addMonthsNoOverflow($i)
                                        ->toDateString();

                                    // Pokok
                                    Angsuran::create([
                                        'pinjaman_id' => $record->id,
                                        'anggota_name' => $record->anggota_name,
                                        'angsuran_ke' => $i,
                                        'jumlah_angsuran' => round($angsuranPokok, 2),
                                        'jenis' => 'pokok',
                                        'is_didenda' => false,
                                        'status' => 'belum_bayar',
                                        'tenggat_waktu' => $tenggatWaktu,
                                    ]);

                                    // Bunga Tetap
                                    Angsuran::create([
                                        'pinjaman_id' => $record->id,
                                        'anggota_name' => $record->anggota_name,
                                        'angsuran_ke' => $i,
                                        'bunga_angsuran' => round($angsuranBunga, 2),
                                        'jenis' => 'bunga',
                                        'is_didenda' => false,
                                        'status' => 'belum_bayar',
                                        'tenggat_waktu' => $tenggatWaktu,
                                    ]);

                                    // Update status nasabah
                                    Nasabah::update([
                                        'status' => 'running',
                                    ]);
                                }
                            } else {
                                // JENIS BUNGA MENURUN
                                $sisaPokok = $record->jumlah_pinjaman;
                                $angsuranPokok = $record->jumlah_pinjaman / $tenor;

                                for ($i = 1; $i <= $tenor; ++$i) {
                                    $tenggatWaktu = $tanggalPinjaman
                                        ->copy()
                                        ->addMonthsNoOverflow($i)
                                        ->toDateString();

                                    $bungaMenurun = $sisaPokok * $persentase;

                                    // Pokok
                                    Angsuran::create([
                                        'pinjaman_id' => $record->id,
                                        'anggota_name' => $record->anggota_name,
                                        'angsuran_ke' => $i,
                                        'jumlah_angsuran' => round($angsuranPokok, 2),
                                        'jenis' => 'pokok',
                                        'is_didenda' => false,
                                        'status' => 'belum_bayar',
                                        'tenggat_waktu' => $tenggatWaktu,
                                    ]);

                                    // Bunga Menurun
                                    Angsuran::create([
                                        'pinjaman_id' => $record->id,
                                        'anggota_name' => $record->anggota_name,
                                        'angsuran_ke' => $i,
                                        'bunga_angsuran' => round($bungaMenurun, 2),
                                        'jenis' => 'bunga',
                                        'is_didenda' => false,
                                        'status' => 'belum_bayar',
                                        'tenggat_waktu' => $tenggatWaktu,
                                    ]);

                                    $sisaPokok -= $angsuranPokok;
                                }
                            }
                        });

                        Notification::make()
                            ->title('Pinjaman disetujui dan angsuran telah dibuat.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function hitungJatuhTempo(Get $get, Set $set): void
    {
        $tanggal = $get('tanggal_pinjaman');
        $tenor = $get('tenor');

        if (!$tanggal || !$tenor) {
            $set('jatuh_tempo', null);

            return;
        }

        $jatuhTempo = Carbon::parse($tanggal)
            ->addMonthsNoOverflow((int) $tenor);

        $set('jatuh_tempo', $jatuhTempo->toDateString());
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPinjamen::route('/'),
            'create' => Pages\CreatePinjaman::route('/create'),
            'edit' => Pages\EditPinjaman::route('/{record}/edit'),
        ];
    }
}
