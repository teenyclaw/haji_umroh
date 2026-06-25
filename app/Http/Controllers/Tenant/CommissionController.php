<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $commissions = Commission::with(['agent', 'booking.package'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'pending' => Commission::where('status', 'pending')->sum('amount'),
            'paid' => Commission::where('status', 'paid')->sum('amount'),
        ];

        return view('tenant.commissions.index', compact('commissions', 'summary'));
    }

    public function pay(Commission $commission)
    {
        $commission->update([
            'status' => 'paid',
            'paid_date' => now()->toDateString(),
        ]);

        log_activity('commission.paid', "Komisi agent {$commission->agent->name} dibayar", $commission);

        return back()->with('success', 'Komisi ditandai sudah dibayar.');
    }
}
