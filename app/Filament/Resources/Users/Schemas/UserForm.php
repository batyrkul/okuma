<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\EmCategory;
use App\Models\Company;
use App\Models\Obiect;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state)),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

            ]);
    }
}
