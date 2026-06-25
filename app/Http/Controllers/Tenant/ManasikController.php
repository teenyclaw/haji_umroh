<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\ManasikSchedule;
use App\Models\Package;
use Illuminate\Http\Request;

class ManasikController extends Controller
{
    public function index()
    {
        $schedules = ManasikSchedule::with('package')
            ->withCount(['attendances as present_count' => fn ($q) => $q->where('present', true)])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('tenant.manasik.index', compact('schedules'));
    }

    public function create()
    {
        return view('tenant.manasik.create', [
            'packages' => Package::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        ManasikSchedule::create($this->validateData($request));

        return redirect()->route('app.manasik.index')
            ->with('success', 'Jadwal manasik dibuat.');
    }

    public function show(ManasikSchedule $manasik)
    {
        $manasik->load('package', 'attendances.jamaah');

        $attendanceMap = $manasik->attendances->keyBy('jamaah_id');

        $jamaah = Jamaah::orderBy('full_name')->get();

        return view('tenant.manasik.show', compact('manasik', 'jamaah', 'attendanceMap'));
    }

    public function edit(ManasikSchedule $manasik)
    {
        return view('tenant.manasik.edit', [
            'manasik' => $manasik,
            'packages' => Package::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ManasikSchedule $manasik)
    {
        $manasik->update($this->validateData($request));

        return redirect()->route('app.manasik.show', $manasik)
            ->with('success', 'Jadwal manasik diperbarui.');
    }

    public function attendance(Request $request, ManasikSchedule $manasik)
    {
        $present = $request->input('present', []);

        $jamaah = Jamaah::pluck('id');

        foreach ($jamaah as $id) {
            $manasik->attendances()->updateOrCreate(
                ['jamaah_id' => $id],
                ['present' => in_array((string) $id, $present, true) || in_array($id, $present)]
            );
        }

        return back()->with('success', 'Absensi disimpan.');
    }

    public function destroy(ManasikSchedule $manasik)
    {
        $manasik->delete();

        return redirect()->route('app.manasik.index')
            ->with('success', 'Jadwal manasik dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string'],
        ]);
    }
}
