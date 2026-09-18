<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\Unit;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Категория товара')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('brand_id')
                    ->label('Бренд')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('name')
                    ->label('Название товара')
                    ->placeholder('Например: Краска интерьерная белая')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sku')
                    ->label('Артикул (SKU)')
                    ->default(fn (): string => Product::generateSku())
                    ->readOnly()
                    ->dehydrated()
                    ->required()
                    ->helperText('Артикул создаётся автоматически'),

                TextInput::make('barcode')
                    ->label('Штрих-код')
                    ->placeholder('Введите заводской штрих-код')
                    ->helperText(
                        'Если у товара есть заводской штрих-код, укажите его'
                    )
                    ->maxLength(255),

                FileUpload::make('image')
                    ->label('Изображение товара')
                    ->image()
                    ->imageEditor()
                    ->directory('products')
                    ->disk('public')
                    ->visibility('public')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(5120)
                    ->helperText('Форматы: JPG, PNG, WEBP. Максимум 5 МБ')
                    ->columnSpanFull(),

                TextInput::make('color')
                    ->label('Название цвета')
                    ->placeholder('Например: Белый')
                    ->maxLength(255),

                TextInput::make('color_code')
                    ->label('Код цвета')
                    ->placeholder('Например: RAL 9010')
                    ->helperText('Код цвета по каталогу производителя')
                    ->maxLength(255),

                TextInput::make('volume')
                    ->label('Объём')
                    ->placeholder('Например: 10')
                    ->numeric()
                    ->minValue(0),

                Select::make('unit')
                    ->label('Единица измерения')
                    ->options(Unit::options())
                    ->default(Unit::PIECE->value)
                    ->required()
                    ->searchable()
                    ->native(false),

                TextInput::make('purchase_price')
                    ->label('Закупочная цена')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('сом'),

                TextInput::make('sale_price')
                    ->label('Цена продажи')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('сом'),

                TextInput::make('min_stock')
                    ->label('Минимальный остаток')
                    ->helperText(
                        'Система предупредит, когда остаток станет меньше'
                    )
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Активный товар')
                    ->helperText('Неактивный товар нельзя будет продавать')
                    ->default(true)
                    ->required(),
            ]);
    }
}
