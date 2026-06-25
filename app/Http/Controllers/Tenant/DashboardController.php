<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\DocumentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\JamaahStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Jamaah;
use App\Models\ManasikSchedule;
use App\Models\Package;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $activeStatuses = [JamaahStatus::Batal->value, JamaahStatus::Selesai->value];

        $stats = [
            'jamaah' => Jamaah::whereNotIn('status', $activeStatuses)->count(),
            'bookings_month' => Booking::whereMonth('booking_date', now()->month)
                ->whereYear('booking_date', now()->year)->count(),
            'piutang' => Invoice::whereIn('status', [InvoiceStatus::Unpaid->value, InvoiceStatus::Partial->value])
                ->get()->sum(fn ($i) => $i->outstanding()),
            'pending_payments' => Payment::where('status', PaymentStatus::Pending)->count(),
            'pending_documents' => Document::where('status', DocumentStatus::Upload)->count(),
            'packages' => Package::where('is_active', true)->count(),
        ];

        $upcomingDepartures = Package::where('is_active', true)
            ->whereNotNull('departure_date')
            ->whereDate('departure_date', '>=', now())
            ->orderBy('departure_date')
            ->take(5)
            ->get();

        $pendingPayments = Payment::with('invoice.booking.package')
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->take(5)
            ->get();

        $expiringPassports = Jamaah::whereNotNull('passport_expired_at')
            ->whereDate('passport_expired_at', '<=', now()->addMonths(6))
            ->whereNotIn('status', $activeStatuses)
            ->orderBy('passport_expired_at')
            ->take(5)
            ->get();

        $statusDistribution = Jamaah::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('tenant.dashboard', compact(
            'stats',
            'upcomingDepartures',
            'pendingPayments',
            'expiringPassports',
            'statusDistribution'
        ));
    }
}
