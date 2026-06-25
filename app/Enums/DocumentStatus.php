<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Belum = 'belum';
    case Upload = 'upload';
    case Terverifikasi = 'terverifikasi';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Belum => 'Belum Upload',
            self::Upload => 'Menunggu Verifikasi',
            self::Terverifikasi => 'Terverifikasi',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Belum => 'slate',
            self::Upload => 'amber',
            self::Terverifikasi => 'emerald',
            self::Ditolak => 'rose',
        };
    }
}
