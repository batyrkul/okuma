<?php

namespace App\Filament\Resources\Schadules\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SchaduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('total')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
