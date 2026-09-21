<?php

namespace App\Filament\Resources\Schadules\Pages;

use App\Filament\Resources\Schadules\SchaduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSchadule extends EditRecord
{
    protected static string $resource = SchaduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
