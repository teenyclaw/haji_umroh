<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Package;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        $packages = Package::where('is_active', true)
            ->where('is_published', true)
            ->with('prices')
            ->orderBy('departure_date')
            ->take(6)
            ->get();

        return view('portal.index', [
            'tenant' => current_tenant(),
            'packages' => $packages,
        ]);
    }

    public function packages(Request $request)
    {
        $packages = Package::where('is_active', true)
            ->where('is_published', true)
            ->with('prices')
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderBy('departure_date')
            ->paginate(9)
            ->withQueryString();

        return view('portal.packages', [
            'tenant' => current_tenant(),
            'packages' => $packages,
        ]);
    }

    public function package(string $tenantSlug, Package $package)
    {
        abort_unless($package->is_published && $package->is_active, 404);

        $package->load('prices');

        return view('portal.package', [
            'tenant' => current_tenant(),
            'package' => $package,
        ]);
    }

    public function storeInquiry(Request $request)
    {
        $data = $request->validate([
            'package_id' => ['nullable', 'exists:packages,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['status'] = 'baru';

        Inquiry::create($data);

        return back()->with('success', 'Terima kasih! Tim kami akan segera menghubungi Anda.');
    }
}
