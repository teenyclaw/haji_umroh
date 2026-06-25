<?php

namespace App\Enums;

enum RoomType: string
{
    case Quad = 'quad';
    case Triple = 'triple';
    case Double = 'double';
    case Single = 'single';

    public function label(): string
    {
        return match ($this) {
            self::Quad => 'Quad (4 orang)',
            self::Triple => 'Triple (3 orang)',
            self::Double => 'Double (2 orang)',
            self::Single => 'Single (1 orang)',
        };
    }

    public function capacity(): int
    {
        return match ($this) {
            self::Quad => 4,
            self::Triple => 3,
            self::Double => 2,
            self::Single => 1,
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
