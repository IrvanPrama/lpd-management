<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfitLossResource\Pages;
use App\Models\ProfitLoss;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class ProfitLossResource extends Resource
{
    protected static ?string $model = ProfitLoss::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bulan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('jenis')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sumber')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_pendapatan')
                    ->numeric(),
                Forms\Components\TextInput::make('total_pengeluaran')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bulan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_pendapatan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_pengeluaran')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Action::make('laporan')
                    ->label('Export PDF')
                    ->color('primary')
                    ->url(fn ($livewire) => route('pdf.profit-loss.pdf', [
                        'status' => $livewire->tableFilters['status']['value'] ?? null,
                        'lapangan_id' => $livewire->tableFilters['lapangan_id']['value'] ?? null,
                        'nama_penyewa' => $livewire->tableFilters['nama_penyewa']['value'] ?? null,
                        'from' => $livewire->tableFilters['tanggal_reservasi']['from'] ?? null,
                        'until' => $livewire->tableFilters['tanggal_reservasi']['until'] ?? null,
                    ])
                    )
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListProfitLosses::route('/'),
            'create' => Pages\CreateProfitLoss::route('/create'),
            'edit' => Pages\EditProfitLoss::route('/{record}/edit'),
        ];
    }
}
