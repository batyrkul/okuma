<?php

namespace App\Filament\Resources\Bolges\Pages;

use App\Filament\Resources\Bolges\BolgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBolges extends ListRecords
{
    protected static string $resource = BolgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
