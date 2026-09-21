<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Aimak;
use Illuminate\Validation\Rule;
class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Аталышы')
                    ->required()
                    ->maxLength(255),

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
                    ->required()->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('bolge_id', null);
                    }),

                Select::make('bolge_id')
                    ->label('Бөлгө')
                    ->relationship(
                        name: 'bolge',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get): Builder =>
                        $query
                            ->where('aimak_id', $get('aimak_id'))
                            ->whereHas(
                                'aimak',
                                fn (Builder $query) => $query->visibleToUser(),
                            ),
                    )
                    ->rules([
                        fn (Get $get) => Rule::exists('bolges', 'id')
                            ->where('aimak_id', $get('aimak_id'))
                            ->whereIn(
                                'aimak_id',
                                Aimak::query()->visibleToUser()->pluck('id')->all(),
                            ),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (Get $get): bool => blank($get('aimak_id')))
                    ->placeholder('Бөлгөнү тандаңыз')
            ]);
    }
}
