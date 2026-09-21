<?php

namespace App\Filament\Resources\Bolges\Pages;

use App\Filament\Resources\Bolges\BolgeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBolge extends EditRecord
{
    protected static string $resource = BolgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
