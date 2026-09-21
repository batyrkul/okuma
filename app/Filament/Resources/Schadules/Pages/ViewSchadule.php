<?php

namespace App\Filament\Resources\Schadules\Pages;

use App\Filament\Resources\Schadules\SchaduleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSchadule extends ViewRecord
{
    protected static string $resource = SchaduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
