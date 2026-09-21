<?php

namespace App\Filament\Resources\Schadules\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class SchaduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Эснаф')
                    ->relationship(
                        name: 'customer',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder =>
                        $query->visibleToUser(),
                    )
                    ->rules([
                        fn () => Rule::exists('customers', 'id')
                            ->whereIn(
                                'id',
                                Customer::query()
                                    ->visibleToUser()
                                    ->select('customers.id')
                                    ->toBase(),
                            ),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Эснафты тандаңыз'),

                DatePicker::make('date')
                    ->label('Дата')
                    ->required(),

                TextInput::make('total')
                    ->label('Сумма')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
