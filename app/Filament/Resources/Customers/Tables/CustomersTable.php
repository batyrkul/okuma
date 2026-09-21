<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Аталышы')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('aimak.name')
                    ->label('Аймак')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Тандалган эмес'),

                TextColumn::make('bolge.name')
                    ->label('Больница')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Тандалган эмес'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
