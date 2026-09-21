<?php

namespace App\Filament\Resources\Schadules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Exports\SchaduleExporter;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchadulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Эснаф')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.aimak.name')
                    ->label('Аймак')
                    ->sortable(),

                TextColumn::make('customer.bolge.name')
                    ->label('Бөлгө')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('бет')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('downloadExcel')
                    ->label('Скачать Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Table $table): StreamedResponse {
                        // Запрос таблицы с поиском, фильтрами и доступом.
                        $query = clone $table
                            ->getLivewire()
                            ->getFilteredTableQuery();

                        $query
                            ->whereHas(
                                'customer',
                                fn (Builder $customerQuery): Builder =>
                                $customerQuery->visibleToUser(),
                            )
                            ->with([
                                'customer.aimak',
                                'customer.bolge',
                            ]);

                        $path = tempnam(sys_get_temp_dir(), 'schadules_');

                        if ($path === false) {
                            throw new \RuntimeException(
                                'Не удалось создать временный файл.'
                            );
                        }

                        $writer = new Writer();
                        $opened = false;

                        try {
                            $writer->openToFile($path);
                            $opened = true;

                            $writer->addRow(Row::fromValues([
                                'ID',
                                'Эснаф',
                                'Аймак',
                                'Бөлгө',
                                'Дата',
                                'Сумма',
                            ]));

                            foreach ($query->lazy(500) as $record) {
                                $writer->addRow(Row::fromValues([
                                    $record->id,
                                    $record->customer?->name ?? '',
                                    $record->customer?->aimak?->name ?? '',
                                    $record->customer?->bolge?->name ?? '',
                                    $record->date
                                        ? \Illuminate\Support\Carbon::parse(
                                        $record->date
                                    )->format('d.m.Y')
                                        : '',
                                    (float) $record->total,
                                ]));
                            }

                            $writer->close();
                            $opened = false;
                        } catch (\Throwable $exception) {
                            if ($opened) {
                                try {
                                    $writer->close();
                                } catch (\Throwable) {
                                    // Сохраняем исходную ошибку.
                                }
                            }

                            @unlink($path);

                            throw $exception;
                        }

                        return response()->streamDownload(
                            function () use ($path): void {
                                try {
                                    readfile($path);
                                } finally {
                                    @unlink($path);
                                }
                            },
                            'schadules_' . now()->format('Y-m-d_H-i-s') . '.xlsx',
                            [
                                'Content-Type' =>
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ],
                        );
                    }),
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
