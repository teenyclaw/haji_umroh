<?php

namespace App\Enums;

enum PackageType: string
{
    case HajiReguler = 'haji_reguler';
    case HajiKhusus = 'haji_khusus';
    case Umroh = 'umroh';
    case UmrohPlus = 'umroh_plus';
    case Tabungan = 'tabungan';

    public function label(): string
    {
        return match ($this) {
            self::HajiReguler => 'Haji Reguler',
            self::HajiKhusus => 'Haji Khusus (Plus)',
            self::Umroh => 'Umroh',
            self::UmrohPlus => 'Umroh Plus',
            self::Tabungan => 'Tabungan',
        };
    }

    public function isHaji(): bool
    {
        return in_array($this, [self::HajiReguler, self::HajiKhusus], true);
    }

    public static function options(): array
    {
        return array_reduce(self::cases(), function (array $carry, self $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}
