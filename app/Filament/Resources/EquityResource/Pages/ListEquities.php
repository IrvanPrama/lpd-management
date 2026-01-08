<?php

namespace App\Filament\Resources\EquityResource\Pages;

use App\Filament\Resources\EquityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquities extends ListRecords
{
    protected static string $resource = EquityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
