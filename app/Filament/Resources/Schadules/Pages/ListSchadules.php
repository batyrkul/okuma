<?php

namespace App\Filament\Resources\Schadules\Pages;

use App\Filament\Resources\Schadules\SchaduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchadules extends ListRecords
{
    protected static string $resource = SchaduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
