<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TabunganResource\Pages;
use App\Models\Nasabah;
use App\Models\Tabungan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TabunganResource extends Resource
{
    protected static ?string $model = Tabungan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('anggota_id')
                    ->required()
                    ->options([
                        Nasabah::all()->pluck('name', 'id')->toArray(),
                    ])
                    ->afterStateUpdated(fn (callable $set, $state) => $set('anggota_name', Nasabah::all()->where('id', $state)->first()->name))
                    ->reactive(),
                Forms\Components\TextInput::make('anggota_name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('jumlah_setoran')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_setor')
                    ->required(),
                Forms\Components\TextInput::make('bukti')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anggota_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                    ->sortable(),
                Tables\Columns\TextColumn::make('anggota_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah_setoran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_setor')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bukti')
                    ->searchable(),
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
            'index' => Pages\ListTabungans::route('/'),
            'create' => Pages\CreateTabungan::route('/create'),
            'edit' => Pages\EditTabungan::route('/{record}/edit'),
        ];
    }
}
