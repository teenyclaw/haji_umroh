<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Gender;
use App\Enums\JamaahStatus;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Jamaah;
use App\Services\DocumentChecklistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JamaahController extends Controller
{
    public function __construct(private DocumentChecklistService $checklist)
    {
    }

    public function index(Request $request)
    {
        $jamaah = Jamaah::with('agent')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', "%{$s}%")
                ->orWhere('nik', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tenant.jamaah.index', [
            'jamaah' => $jamaah,
            'statuses' => JamaahStatus::options(),
        ]);
    }

    public function create()
    {
        return view('tenant.jamaah.create', [
            'genders' => Gender::options(),
            'statuses' => JamaahStatus::options(),
            'agents' => Agent::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('jamaah', 'public');
        }

        $jamaah = Jamaah::create($data);
        $this->checklist->syncForJamaah($jamaah);

        log_activity('jamaah.created', "Jamaah {$jamaah->full_name} ditambahkan", $jamaah);

        return redirect()->route('app.jamaah.show', $jamaah)
            ->with('success', 'Data jamaah berhasil disimpan.');
    }

    public function show(Jamaah $jamaah)
    {
        $jamaah->load(['agent', 'documents.documentType', 'bookingJamaah.booking.package']);

        return view('tenant.jamaah.show', [
            'jamaah' => $jamaah,
            'completion' => $this->checklist->completion($jamaah),
        ]);
    }

    public function edit(Jamaah $jamaah)
    {
        return view('tenant.jamaah.edit', [
            'jamaah' => $jamaah,
            'genders' => Gender::options(),
            'statuses' => JamaahStatus::options(),
            'agents' => Agent::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Jamaah $jamaah)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('photo')) {
            if ($jamaah->photo_path) {
                Storage::disk('public')->delete($jamaah->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('jamaah', 'public');
        }

        $jamaah->update($data);

        log_activity('jamaah.updated', "Jamaah {$jamaah->full_name} diperbarui", $jamaah);

        return redirect()->route('app.jamaah.show', $jamaah)
            ->with('success', 'Data jamaah diperbarui.');
    }

    public function destroy(Jamaah $jamaah)
    {
        $jamaah->delete();

        return redirect()->route('app.jamaah.index')
            ->with('success', 'Data jamaah dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'nik' => ['nullable', 'digits:16'],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'in:L,P'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issued_at' => ['nullable', 'date'],
            'passport_expired_at' => ['nullable', 'date'],
            'passport_office' => ['nullable', 'string', 'max:255'],
            'mahram_relation' => ['nullable', 'string', 'max:100'],
            'mahram_name' => ['nullable', 'string', 'max:255'],
            'emergency_name' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'shirt_size' => ['nullable', 'string', 'max:20'],
            'health_notes' => ['nullable', 'string'],
            'porsi_number' => ['nullable', 'string', 'max:50'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
