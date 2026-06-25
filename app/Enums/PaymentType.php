<?php

namespace App\Enums;

enum PaymentType: string
{
    case Dp = 'dp';
    case Cicilan = 'cicilan';
    case Pelunasan = 'pelunasan';

    public function label(): string
    {
        return match ($this) {
            self::Dp => 'Uang Muka (DP)',
            self::Cicilan => 'Cicilan',
            self::Pelunasan => 'Pelunasan',
        };
    }

    public static function options(): array
    {
        return array_reduce(self::cases(), function (array $carry, self $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}
