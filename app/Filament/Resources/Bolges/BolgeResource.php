<?php

namespace App\Filament\Resources\Bolges;

use App\Filament\Resources\Bolges\Pages\CreateBolge;
use App\Filament\Resources\Bolges\Pages\EditBolge;
use App\Filament\Resources\Bolges\Pages\ListBolges;
use App\Filament\Resources\Bolges\Pages\ViewBolge;
use App\Filament\Resources\Bolges\Schemas\BolgeForm;
use App\Filament\Resources\Bolges\Schemas\BolgeInfolist;
use App\Filament\Resources\Bolges\Tables\BolgesTable;
use App\Models\Bolge;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class BolgeResource extends Resource
{
    protected static ?string $model = Bolge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Backward;

    protected static string|UnitEnum|null $navigationGroup =  'Аймактар';
    protected static ?string $navigationLabel = 'Болголор';
    protected static ?string $pluralModelLabel = 'Болголор';
    protected static ?string $recordTitleAttribute = 'Болголор';

    public static function form(Schema $schema): Schema
    {
        return BolgeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BolgeInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('aimak', fn (Builder $query) => $query->visibleToUser());
    }

    public static function table(Table $table): Table
    {
        return BolgesTable::configure($table);
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
            'index' => ListBolges::route('/'),
            'create' => CreateBolge::route('/create'),
            'view' => ViewBolge::route('/{record}'),
            'edit' => EditBolge::route('/{record}/edit'),
        ];
    }
}
