<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function __construct(private TenantProvisioner $provisioner)
    {
    }

    public function index(Request $request)
    {
        $tenants = Tenant::withCount(['users', 'packages', 'jamaah'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants', 'slug')],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'city' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', Rule::unique('users', 'email')],
            'owner_password' => ['required', 'string', 'min:6'],
        ]);

        $tenant = $this->provisioner->provision($data);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Travel berhasil dibuat dengan akun owner dan data awal.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount(['users', 'packages', 'jamaah', 'bookings']);
        $users = $tenant->users()->with('roles')->get();

        return view('superadmin.tenants.show', compact('tenant', 'users'));
    }

    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants', 'slug')->ignore($tenant)],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'city' => ['nullable', 'string', 'max:255'],
            'subscription_plan' => ['required', 'string'],
        ]);

        $tenant->update($data);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Data travel diperbarui.');
    }

    public function toggle(Tenant $tenant)
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        return back()->with('success', 'Status travel diperbarui.');
    }

    public function resetOwnerPassword(Request $request, Tenant $tenant)
    {
        $request->validate(['password' => ['required', 'min:6']]);

        $owner = $tenant->users()->first();

        if ($owner) {
            $owner->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Kata sandi owner diperbarui.');
    }
}
