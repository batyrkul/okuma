<?php

namespace App\Filament\Resources\Aimaks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AimakForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('permission')->label('permission'),
            ]);
    }
}
