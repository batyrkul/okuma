<?php

namespace App\Enums;

enum Unit: string
{
    case PIECE = 'piece';
    case KILOGRAM = 'kilogram';
    case GRAM = 'gram';
    case LITER = 'liter';
    case MILLILITER = 'milliliter';
    case METER = 'meter';
    case SQUARE_METER = 'square_meter';
    case CUBIC_METER = 'cubic_meter';
    case PACK = 'pack';
    case BOX = 'box';

    public function label(): string
    {
        return match ($this) {
            self::PIECE => 'Штука',
            self::KILOGRAM => 'Килограмм',
            self::GRAM => 'Грамм',
            self::LITER => 'Литр',
            self::MILLILITER => 'Миллилитр',
            self::METER => 'Метр',
            self::SQUARE_METER => 'Квадратный метр',
            self::CUBIC_METER => 'Кубический метр',
            self::PACK => 'Упаковка',
            self::BOX => 'Коробка',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::PIECE => 'шт.',
            self::KILOGRAM => 'кг',
            self::GRAM => 'г',
            self::LITER => 'л',
            self::MILLILITER => 'мл',
            self::METER => 'м',
            self::SQUARE_METER => 'м²',
            self::CUBIC_METER => 'м³',
            self::PACK => 'упак.',
            self::BOX => 'кор.',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $unit) => [
                $unit->value => $unit->label(),
            ])
            ->all();
    }
}
