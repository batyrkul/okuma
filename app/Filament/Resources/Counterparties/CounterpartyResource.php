<?php

namespace App\Filament\Resources\Counterparties;

use App\Filament\Resources\Counterparties\Pages\CreateCounterparty;
use App\Filament\Resources\Counterparties\Pages\EditCounterparty;
use App\Filament\Resources\Counterparties\Pages\ListCounterparties;
use App\Filament\Resources\Counterparties\Pages\ViewCounterparty;
use App\Filament\Resources\Counterparties\Schemas\CounterpartyForm;
use App\Filament\Resources\Counterparties\Schemas\CounterpartyInfolist;
use App\Filament\Resources\Counterparties\Tables\CounterpartiesTable;
use App\Models\Counterparty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CounterpartyResource extends Resource
{
    protected static ?string $model = Counterparty::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Настройки';

    protected static ?string $navigationLabel = 'Список контрагентов';

    protected static ?string $modelLabel = 'Контрагент';

    protected static ?string $pluralModelLabel = 'Контрагенты';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CounterpartyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CounterpartyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CounterpartiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCounterparties::route('/'),
            'create' => CreateCounterparty::route('/create'),
            'view' => ViewCounterparty::route('/{record}'),
            'edit' => EditCounterparty::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
