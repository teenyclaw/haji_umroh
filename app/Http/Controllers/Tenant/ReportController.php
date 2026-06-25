<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Exports\JamaahExport;
use App\Exports\PaymentExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Jamaah;
use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $verifiedPayments = Payment::where('status', PaymentStatus::Verified);

        $finance = [
            'total_masuk' => (clone $verifiedPayments)->sum('amount'),
            'masuk_bulan_ini' => (clone $verifiedPayments)
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'piutang' => Invoice::whereIn('status', [InvoiceStatus::Unpaid->value, InvoiceStatus::Partial->value])
                ->get()->sum(fn ($i) => $i->outstanding()),
        ];

        $unpaidInvoices = Invoice::with('booking.package')
            ->whereIn('status', [InvoiceStatus::Unpaid->value, InvoiceStatus::Partial->value])
            ->latest()
            ->take(15)
            ->get();

        $monthlyIncome = Payment::where('status', PaymentStatus::Verified)
            ->whereYear('payment_date', now()->year)
            ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $jamaahByStatus = Jamaah::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('tenant.reports.index', compact('finance', 'unpaidInvoices', 'monthlyIncome', 'jamaahByStatus'));
    }

    public function exportJamaah()
    {
        return Excel::download(new JamaahExport, 'jamaah-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPayments()
    {
        return Excel::download(new PaymentExport, 'pembayaran-' . now()->format('Ymd') . '.xlsx');
    }
}
