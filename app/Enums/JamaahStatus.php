<?php

namespace App\Enums;

enum JamaahStatus: string
{
    case Prospek = 'prospek';
    case Booking = 'booking';
    case Dp = 'dp';
    case Cicilan = 'cicilan';
    case Lunas = 'lunas';
    case DokumenLengkap = 'dokumen_lengkap';
    case Manasik = 'manasik';
    case Berangkat = 'berangkat';
    case Selesai = 'selesai';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::Prospek => 'Prospek',
            self::Booking => 'Booking',
            self::Dp => 'DP',
            self::Cicilan => 'Cicilan',
            self::Lunas => 'Lunas',
            self::DokumenLengkap => 'Dokumen Lengkap',
            self::Manasik => 'Manasik',
            self::Berangkat => 'Berangkat',
            self::Selesai => 'Selesai',
            self::Batal => 'Batal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Prospek => 'slate',
            self::Booking => 'blue',
            self::Dp => 'amber',
            self::Cicilan => 'amber',
            self::Lunas => 'emerald',
            self::DokumenLengkap => 'teal',
            self::Manasik => 'indigo',
            self::Berangkat => 'violet',
            self::Selesai => 'green',
            self::Batal => 'rose',
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
