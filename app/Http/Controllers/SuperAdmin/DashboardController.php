<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Jamaah;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('is_active', true)->count(),
            'users' => User::where('is_super_admin', false)->count(),
            'jamaah' => Jamaah::withoutGlobalScope('tenant')->count(),
            'bookings' => Booking::withoutGlobalScope('tenant')->count(),
        ];

        $tenants = Tenant::withCount('users')
            ->latest()
            ->take(8)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'tenants'));
    }
}
