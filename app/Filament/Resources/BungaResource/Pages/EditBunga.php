<?php

namespace App\Filament\Resources\BungaResource\Pages;

use App\Filament\Resources\BungaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBunga extends EditRecord
{
    protected static string $resource = BungaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
