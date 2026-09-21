<?php

namespace App\Filament\Resources\Aimaks;

use App\Filament\Resources\Aimaks\Pages\CreateAimak;
use App\Filament\Resources\Aimaks\Pages\EditAimak;
use App\Filament\Resources\Aimaks\Pages\ListAimaks;
use App\Filament\Resources\Aimaks\Pages\ViewAimak;
use App\Filament\Resources\Aimaks\Schemas\AimakForm;
use App\Filament\Resources\Aimaks\Schemas\AimakInfolist;
use App\Filament\Resources\Aimaks\Tables\AimaksTable;
use App\Models\Aimak;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;
class AimakResource extends Resource
{
    protected static ?string $model = Aimak::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup =  'Аймактар';
    protected static ?string $navigationLabel = 'Аймактар';
    protected static ?string $pluralModelLabel = 'Аймактар';

    protected static ?string $recordTitleAttribute = 'Аймактар';

    public static function form(Schema $schema): Schema
    {
        return AimakForm::configure($schema);
    }

    public static function canCreate(): bool
    {
        return (bool) filament()->auth()->user()?->hasRole('Супер админ')
            && parent::canCreate();
    }

    public static function canEdit(
        \Illuminate\Database\Eloquent\Model $record
    ): bool {
        return (bool) filament()->auth()->user()?->hasRole('Супер админ')
            && parent::canEdit($record);
    }

    public static function canDelete(
        \Illuminate\Database\Eloquent\Model $record
    ): bool {
        return (bool) filament()->auth()->user()?->hasRole('Супер админ')
            && parent::canDelete($record);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) filament()->auth()->user()?->hasRole('Супер админ')
            && parent::canDeleteAny();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleToUser();
    }

    public static function infolist(Schema $schema): Schema
    {
        return AimakInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AimaksTable::configure($table);
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
            'index' => ListAimaks::route('/'),
            'create' => CreateAimak::route('/create'),
            'view' => ViewAimak::route('/{record}'),
            'edit' => EditAimak::route('/{record}/edit'),
        ];
    }
}
