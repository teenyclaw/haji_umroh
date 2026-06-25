<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\PackageType;
use App\Enums\RoomType;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::withCount('bookings')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('tenant.packages.index', [
            'packages' => $packages,
            'types' => PackageType::options(),
        ]);
    }

    public function create()
    {
        return view('tenant.packages.create', [
            'types' => PackageType::options(),
            'roomTypes' => RoomType::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('packages', 'public');
        }

        $package = Package::create($data);
        $this->syncPrices($package, $request);

        log_activity('package.created', "Paket {$package->name} dibuat", $package);

        return redirect()->route('app.packages.show', $package)
            ->with('success', 'Paket berhasil dibuat.');
    }

    public function show(Package $package)
    {
        $package->load('prices', 'bookings.invoice');

        return view('tenant.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $package->load('prices');

        return view('tenant.packages.edit', [
            'package' => $package,
            'types' => PackageType::options(),
            'roomTypes' => RoomType::cases(),
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            if ($package->image_path) {
                Storage::disk('public')->delete($package->image_path);
            }
            $data['image_path'] = $request->file('image')->store('packages', 'public');
        }

        $package->update($data);
        $this->syncPrices($package, $request);

        log_activity('package.updated', "Paket {$package->name} diperbarui", $package);

        return redirect()->route('app.packages.show', $package)
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('app.packages.index')
            ->with('success', 'Paket dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'duration_days' => ['nullable', 'integer', 'min:0'],
            'departure_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'airline' => ['nullable', 'string', 'max:255'],
            'departure_airport' => ['nullable', 'string', 'max:255'],
            'hotel_makkah' => ['nullable', 'string', 'max:255'],
            'hotel_makkah_star' => ['nullable', 'integer', 'min:1', 'max:5'],
            'hotel_makkah_distance' => ['nullable', 'string', 'max:255'],
            'hotel_madinah' => ['nullable', 'string', 'max:255'],
            'hotel_madinah_star' => ['nullable', 'integer', 'min:1', 'max:5'],
            'hotel_madinah_distance' => ['nullable', 'string', 'max:255'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'handling_fee' => ['nullable', 'numeric', 'min:0'],
            'insurance_fee' => ['nullable', 'numeric', 'min:0'],
            'visa_fee' => ['nullable', 'numeric', 'min:0'],
            'ppiu_number' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    protected function syncPrices(Package $package, Request $request): void
    {
        $prices = $request->input('prices', []);

        foreach ($prices as $roomType => $price) {
            if ($price === null || $price === '') {
                $package->prices()->where('room_type', $roomType)->delete();
                continue;
            }

            $package->prices()->updateOrCreate(
                ['room_type' => $roomType],
                ['price' => $price]
            );
        }
    }
}
