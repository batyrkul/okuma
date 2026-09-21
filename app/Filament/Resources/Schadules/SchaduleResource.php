<?php

namespace App\Filament\Resources\Schadules;

use App\Filament\Resources\Schadules\Pages\CreateSchadule;
use App\Filament\Resources\Schadules\Pages\EditSchadule;
use App\Filament\Resources\Schadules\Pages\ListSchadules;
use App\Filament\Resources\Schadules\Pages\ViewSchadule;
use App\Filament\Resources\Schadules\Schemas\SchaduleForm;
use App\Filament\Resources\Schadules\Schemas\SchaduleInfolist;
use App\Filament\Resources\Schadules\Tables\SchadulesTable;
use App\Models\Schadule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class SchaduleResource extends Resource
{
    protected static ?string $model = Schadule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup =  'Аймактар';
    protected static ?string $navigationLabel = 'Окууган беттер';
    protected static ?string $pluralModelLabel = 'Окууган беттер';
    protected static ?string $recordTitleAttribute = 'Окууган беттер';

    public static function form(Schema $schema): Schema
    {
        return SchaduleForm::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas(
                'customer',
                fn (Builder $query) => $query->visibleToUser(),
            );
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchaduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchadulesTable::configure($table);
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
            'index' => ListSchadules::route('/'),
            'create' => CreateSchadule::route('/create'),
            'view' => ViewSchadule::route('/{record}'),
            'edit' => EditSchadule::route('/{record}/edit'),
        ];
    }
}
