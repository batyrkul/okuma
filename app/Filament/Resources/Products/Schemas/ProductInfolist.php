<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.name')
                    ->label('Category')
                    ->placeholder('-'),
                TextEntry::make('unit.name')
                    ->label('Unit'),
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('barcode')
                    ->placeholder('-'),
                TextEntry::make('brand')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('color')
                    ->placeholder('-'),
                TextEntry::make('volume')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('purchase_price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money(),
                TextEntry::make('min_stock')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Product $record): bool => $record->trashed()),
                TextEntry::make('unit')
                    ->placeholder('-'),
            ]);
    }
}
