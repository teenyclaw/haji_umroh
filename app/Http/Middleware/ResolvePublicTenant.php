<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('tenantSlug');

        $tenant = Tenant::where('slug', $slug)->where('is_active', true)->first();

        if (! $tenant) {
            abort(404, 'Travel tidak ditemukan.');
        }

        tenancy()->set($tenant);
        view()->share('portalTenant', $tenant);

        return $next($request);
    }
}
