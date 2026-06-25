<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\BookingStatus;
use App\Enums\Gender;
use App\Enums\RoomType;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Jamaah;
use App\Models\Package;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(private BookingService $service)
    {
    }

    public function index(Request $request)
    {
        $bookings = Booking::with(['package', 'agent', 'invoice'])
            ->withCount('jamaahPivot')
            ->when($request->search, fn ($q, $s) => $q->where('code', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tenant.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::options(),
        ]);
    }

    public function create(Request $request)
    {
        $packages = Package::where('is_active', true)->with('prices')->orderBy('name')->get();
        $jamaah = Jamaah::orderBy('full_name')->get();
        $agents = Agent::where('is_active', true)->orderBy('name')->get();

        return view('tenant.bookings.create', [
            'packages' => $packages,
            'jamaahList' => $jamaah,
            'agents' => $agents,
            'roomTypes' => RoomType::options(),
            'selectedPackage' => $request->package_id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'notes' => ['nullable', 'string'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.jamaah_id' => ['required', 'distinct', 'exists:jamaah,id'],
            'participants.*.room_type' => ['required', 'in:quad,triple,double,single'],
        ], [], [
            'participants.*.jamaah_id' => 'jamaah',
            'participants.*.room_type' => 'tipe kamar',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        $this->ensureMahramRules($validated['participants']);

        $booking = $this->service->create(
            $package,
            $validated['participants'],
            $validated['agent_id'] ?? null,
            $validated['notes'] ?? null
        );

        return redirect()->route('app.bookings.show', $booking)
            ->with('success', "Booking {$booking->code} berhasil dibuat beserta invoice.");
    }

    public function show(Booking $booking)
    {
        $booking->load(['package', 'agent', 'jamaahPivot.jamaah', 'invoice.payments', 'commission.agent']);

        return view('tenant.bookings.show', compact('booking'));
    }

    public function destroy(Booking $booking)
    {
        $booking->update(['status' => BookingStatus::Cancelled]);

        return redirect()->route('app.bookings.index')
            ->with('success', 'Booking dibatalkan.');
    }

    protected function ensureMahramRules(array $participants): void
    {
        if (! current_tenant()?->mahram_strict) {
            return;
        }

        $ids = array_column($participants, 'jamaah_id');
        $females = Jamaah::whereIn('id', $ids)
            ->where('gender', Gender::P->value)
            ->whereNull('mahram_name')
            ->where(function ($q) {
                $q->whereNull('mahram_relation')->orWhere('mahram_relation', '');
            })
            ->get();

        if ($females->isNotEmpty()) {
            throw ValidationException::withMessages([
                'participants' => 'Jamaah perempuan berikut belum memiliki data mahram: '
                    . $females->pluck('full_name')->join(', ')
                    . '. Lengkapi data mahram atau nonaktifkan validasi mahram di Pengaturan.',
            ]);
        }
    }
}
