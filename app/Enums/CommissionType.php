<?php

namespace App\Enums;

enum CommissionType: string
{
    case Flat = 'flat';
    case Percentage = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::Flat => 'Nominal Tetap (per jamaah)',
            self::Percentage => 'Persentase (dari harga paket)',
        };
    }

    public static function options(): array
    {
        return [
            self::Flat->value => self::Flat->label(),
            self::Percentage->value => self::Percentage->label(),
        ];
    }
}
