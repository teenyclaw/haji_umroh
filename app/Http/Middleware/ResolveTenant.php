<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (! $user->tenant_id || ! $user->tenant) {
            abort(403, 'Akun Anda belum terhubung dengan travel manapun.');
        }

        if (! $user->tenant->is_active) {
            abort(403, 'Travel Anda sedang non-aktif. Hubungi administrator platform.');
        }

        tenancy()->set($user->tenant);
        view()->share('currentTenant', $user->tenant);

        return $next($request);
    }
}
