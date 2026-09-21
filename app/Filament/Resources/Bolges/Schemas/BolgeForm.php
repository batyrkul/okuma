<?php

namespace App\Filament\Resources\Bolges\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Aimak;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class BolgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('aimak_id')
                    ->label('Аймак')
                    ->relationship(
                        name: 'aimak',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder =>
                        $query->visibleToUser(),
                    )
                    ->rules([
                        fn () => Rule::exists('aimaks', 'id')->whereIn(
                            'id',
                            Aimak::query()->visibleToUser()->pluck('id')->all(),
                        ),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
            ]);
    }
}
