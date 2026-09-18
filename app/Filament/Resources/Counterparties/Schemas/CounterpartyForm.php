<?php

namespace App\Filament\Resources\Counterparties\Schemas;

use App\Enums\CounterpartyType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CounterpartyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(CounterpartyType::options())
                    ->default('both')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('inn'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('address'),

                Textarea::make('note')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
