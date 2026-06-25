<?php

namespace App\Enums;

enum Gender: string
{
    case L = 'L';
    case P = 'P';

    public function label(): string
    {
        return match ($this) {
            self::L => 'Laki-laki',
            self::P => 'Perempuan',
        };
    }

    public static function options(): array
    {
        return [
            self::L->value => self::L->label(),
            self::P->value => self::P->label(),
        ];
    }
}
