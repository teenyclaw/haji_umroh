<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('booking.package')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('number', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tenant.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['booking.package', 'booking.jamaahPivot.jamaah', 'payments']);

        return view('tenant.invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['booking.package', 'booking.jamaahPivot.jamaah', 'payments', 'tenant']);

        $pdf = Pdf::loadView('tenant.invoices.pdf', [
            'invoice' => $invoice,
            'tenant' => current_tenant(),
        ]);

        return $pdf->download("Invoice-{$invoice->number}.pdf");
    }
}
