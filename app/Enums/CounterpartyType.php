<?php

namespace App\Enums;

enum CounterpartyType: string
{
    case SUPPLIER = 'supplier';
    case CUSTOMER = 'customer';
    case BOTH = 'both';

    public function label(): string
    {
        return match ($this) {
            self::SUPPLIER => 'Поставщик',
            self::CUSTOMER => 'Покупатель',
            self::BOTH => 'Поставщик и покупатель',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(
                fn (self $type): array => [
                    $type->value => $type->label(),
                ]
            )
            ->all();
    }


}
