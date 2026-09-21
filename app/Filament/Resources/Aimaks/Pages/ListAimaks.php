<?php

namespace App\Filament\Resources\Aimaks\Pages;

use App\Filament\Resources\Aimaks\AimakResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAimaks extends ListRecords
{
    protected static string $resource = AimakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
