<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Permissions\Pages\EditPermission;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Pages\ViewPermission;
use App\Filament\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Resources\Permissions\Schemas\PermissionInfolist;
use App\Filament\Resources\Permissions\Tables\PermissionsTable;
use App\Models\Permission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedKey;

    /**
     * Раздел меню.
     */
    protected static string|UnitEnum|null $navigationGroup = 'Настройки';

    /**
     * Название пункта меню.
     */
    protected static ?string $navigationLabel = 'Разрешения';

    /**
     * Заголовок страницы списка.
     */
    protected static ?string $modelLabel = 'Разрешение';

    protected static ?string $pluralModelLabel = 'Разрешения';

    /**
     * Поле, используемое как название записи.
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Порядок внутри раздела «Настройки».
     */
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return (bool) filament()->auth()->user()?->hasRole('Супер админ')
            && parent::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess()
            && parent::shouldRegisterNavigation();
    }

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view'   => ViewPermission::route('/{record}'),
            'edit'   => EditPermission::route('/{record}/edit'),
        ];
    }
}
