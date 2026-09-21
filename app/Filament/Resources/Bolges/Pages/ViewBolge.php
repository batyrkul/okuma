<?php

namespace App\Filament\Resources\Bolges\Pages;

use App\Filament\Resources\Bolges\BolgeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBolge extends ViewRecord
{
    protected static string $resource = BolgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
