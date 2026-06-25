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
