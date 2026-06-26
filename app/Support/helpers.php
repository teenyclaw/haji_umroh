<?php

use App\Models\ActivityLog;
use App\Models\Tenant;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (! function_exists('tenancy')) {
    function tenancy(): Tenancy
    {
        return app(Tenancy::class);
    }
}

if (! function_exists('current_tenant')) {
    function current_tenant(): ?Tenant
    {
        return app(Tenancy::class)->get();
    }
}

if (! function_exists('current_tenant_id')) {
    function current_tenant_id(): ?int
    {
        return app(Tenancy::class)->id();
    }
}

if (! function_exists('log_activity')) {
    function log_activity(string $action, ?string $description = null, $subject = null): void
    {
        ActivityLog::create([
            'tenant_id' => current_tenant_id(),
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
        ]);
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $value): string
    {
        return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    function tanggal_id($date, bool $withDay = false): string
    {
        if (empty($date)) {
            return '-';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->locale('id');

        return $withDay
            ? $carbon->translatedFormat('l, d F Y')
            : $carbon->translatedFormat('d F Y');
    }
}

if (! function_exists('terbilang')) {
    function terbilang(int|float|null $value, bool $withSuffix = true): string
    {
        $angka = abs((int) round((float) ($value ?? 0)));

        if ($angka === 0) {
            return $withSuffix ? 'nol rupiah' : 'nol';
        }

        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $kata = '';

        if ($angka < 12) {
            $kata = $satuan[$angka];
        } elseif ($angka < 20) {
            $kata = terbilang($angka - 10, false) . ' belas';
        } elseif ($angka < 100) {
            $kata = terbilang((int) floor($angka / 10), false) . ' puluh' . ($angka % 10 ? ' ' . terbilang($angka % 10, false) : '');
        } elseif ($angka < 200) {
            $kata = 'seratus' . ($angka > 100 ? ' ' . terbilang($angka - 100, false) : '');
        } elseif ($angka < 1000) {
            $kata = terbilang((int) floor($angka / 100), false) . ' ratus' . ($angka % 100 ? ' ' . terbilang($angka % 100, false) : '');
        } elseif ($angka < 2000) {
            $kata = 'seribu' . ($angka > 1000 ? ' ' . terbilang($angka - 1000, false) : '');
        } elseif ($angka < 1000000) {
            $kata = terbilang((int) floor($angka / 1000), false) . ' ribu' . ($angka % 1000 ? ' ' . terbilang($angka % 1000, false) : '');
        } elseif ($angka < 1000000000) {
            $kata = terbilang((int) floor($angka / 1000000), false) . ' juta' . ($angka % 1000000 ? ' ' . terbilang($angka % 1000000, false) : '');
        } elseif ($angka < 1000000000000) {
            $kata = terbilang((int) floor($angka / 1000000000), false) . ' miliar' . ($angka % 1000000000 ? ' ' . terbilang($angka % 1000000000, false) : '');
        } else {
            $kata = 'jumlah terlalu besar';
        }

        $kata = trim(preg_replace('/\s+/', ' ', $kata));

        return $withSuffix ? $kata . ' rupiah' : $kata;
    }
}
