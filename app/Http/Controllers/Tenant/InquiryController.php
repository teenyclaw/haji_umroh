<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\JamaahStatus;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Jamaah;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $inquiries = Inquiry::with('package')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('tenant.inquiries.index', compact('inquiries'));
    }

    public function convert(Inquiry $inquiry)
    {
        $jamaah = Jamaah::create([
            'full_name' => $inquiry->name,
            'phone' => $inquiry->phone,
            'whatsapp' => $inquiry->phone,
            'email' => $inquiry->email,
            'status' => JamaahStatus::Prospek,
            'notes' => $inquiry->message,
        ]);

        $inquiry->update(['status' => 'diproses']);

        return redirect()->route('app.jamaah.edit', $jamaah)
            ->with('success', 'Inquiry dikonversi menjadi prospek jamaah. Lengkapi datanya.');
    }

    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $request->validate(['status' => ['required', 'string']]);

        $inquiry->update(['status' => $request->status]);

        return back()->with('success', 'Status inquiry diperbarui.');
    }
}
