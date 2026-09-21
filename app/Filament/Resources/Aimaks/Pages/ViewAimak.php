<?php

namespace App\Filament\Resources\Aimaks\Pages;

use App\Filament\Resources\Aimaks\AimakResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAimak extends ViewRecord
{
    protected static string $resource = AimakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
