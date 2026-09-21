<?php

namespace App\Filament\Exports;

use App\Models\Schadule;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SchaduleExporter extends Exporter
{
    protected static ?string $model = Schadule::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('customer.name')
                ->label('Эснаф'),

            ExportColumn::make('customer.aimak.name')
                ->label('Аймак'),

            ExportColumn::make('customer.bolge.name')
                ->label('Бөлгө'),

            ExportColumn::make('date')
                ->label('Дата'),

            ExportColumn::make('total')
                ->label('Сумма'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Выгружено записей: ' . $export->successful_rows . '.';

        if ($failedRows = $export->getFailedRowsCount()) {
            $body .= ' Не удалось выгрузить: ' . $failedRows . '.';
        }

        return $body;
    }
}
