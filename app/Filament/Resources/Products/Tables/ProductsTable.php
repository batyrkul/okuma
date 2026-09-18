<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Enums\Unit;
use Filament\Tables\Columns\ImageColumn;
class ProductsTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Фото')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(asset('images/no-image.png')),

                TextColumn::make('unit')
                    ->label('Ед. измерения')
                    ->badge()
                    ->formatStateUsing(
                        fn (Unit|string|null $state): string => match (true) {
                            $state instanceof Unit => $state->shortLabel(),
                            is_string($state) => Unit::tryFrom($state)?->shortLabel() ?? $state,
                            default => '—',
                        }
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('color')
                    ->searchable(),
                TextColumn::make('volume')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('min_stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label('Бренд')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
