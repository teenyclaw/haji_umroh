<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\BookingJamaah;
use App\Models\Package;
use App\Models\Rombongan;
use App\Services\BookingService;
use Illuminate\Http\Request;

class RombonganController extends Controller
{
    public function __construct(private BookingService $service)
    {
    }

    public function index()
    {
        $rombongan = Rombongan::with('package')
            ->withCount('members')
            ->latest()
            ->paginate(15);

        return view('tenant.rombongan.index', compact('rombongan'));
    }

    public function create()
    {
        return view('tenant.rombongan.create', [
            'packages' => Package::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['code'] = $this->service->generateCode('ROM');

        $rombongan = Rombongan::create($data);

        return redirect()->route('app.rombongan.show', $rombongan)
            ->with('success', 'Rombongan dibuat.');
    }

    public function show(Rombongan $rombongan)
    {
        $rombongan->load(['package', 'members.jamaah', 'members.booking']);

        $available = BookingJamaah::with('jamaah', 'booking.package')
            ->whereNull('rombongan_id')
            ->when($rombongan->package_id, fn ($q) => $q->whereHas('booking', fn ($b) => $b->where('package_id', $rombongan->package_id)))
            ->get();

        return view('tenant.rombongan.show', compact('rombongan', 'available'));
    }

    public function edit(Rombongan $rombongan)
    {
        return view('tenant.rombongan.edit', [
            'rombongan' => $rombongan,
            'packages' => Package::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Rombongan $rombongan)
    {
        $rombongan->update($this->validateData($request));

        return redirect()->route('app.rombongan.show', $rombongan)
            ->with('success', 'Rombongan diperbarui.');
    }

    public function assign(Request $request, Rombongan $rombongan)
    {
        $request->validate([
            'booking_jamaah_ids' => ['required', 'array'],
            'booking_jamaah_ids.*' => ['exists:booking_jamaah,id'],
        ]);

        BookingJamaah::whereIn('id', $request->booking_jamaah_ids)
            ->update(['rombongan_id' => $rombongan->id]);

        return back()->with('success', 'Jamaah ditambahkan ke rombongan.');
    }

    public function unassign(BookingJamaah $member)
    {
        $member->update(['rombongan_id' => null, 'seat_number' => null]);

        return back()->with('success', 'Jamaah dikeluarkan dari rombongan.');
    }

    public function destroy(Rombongan $rombongan)
    {
        $rombongan->members()->update(['rombongan_id' => null]);
        $rombongan->delete();

        return redirect()->route('app.rombongan.index')
            ->with('success', 'Rombongan dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'leader_name' => ['nullable', 'string', 'max:255'],
            'muthowif_name' => ['nullable', 'string', 'max:255'],
            'bus_number' => ['nullable', 'string', 'max:50'],
            'departure_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
