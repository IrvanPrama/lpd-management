<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquityResource\Pages;
use App\Filament\Resources\EquityResource\RelationManagers;
use App\Models\Equity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquityResource extends Resource
{
    protected static ?string $model = Equity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('anggota_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('anggota_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sumber')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('keterangan')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
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
                Tables\Columns\TextColumn::make('anggota_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquities::route('/'),
            'create' => Pages\CreateEquity::route('/create'),
            'edit' => Pages\EditEquity::route('/{record}/edit'),
        ];
    }
}
