<?php

namespace App\Filament\Resources\Aimaks\Pages;

use App\Filament\Resources\Aimaks\AimakResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAimak extends EditRecord
{
    protected static string $resource = AimakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
