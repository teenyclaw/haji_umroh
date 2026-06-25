<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private BookingService $bookingService,
    ) {
    }

    public function index(Request $request)
    {
        $payments = Payment::with('invoice.booking.package')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tenant.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentStatus::options(),
        ]);
    }

    public function create(Request $request)
    {
        $invoices = Invoice::with('booking.package')
            ->whereIn('status', ['unpaid', 'partial'])
            ->latest()
            ->get();

        return view('tenant.payments.create', [
            'invoices' => $invoices,
            'types' => PaymentType::options(),
            'selectedInvoice' => $request->invoice_id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'type' => ['required', 'in:dp,cicilan,pelunasan'],
            'method' => ['required', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'proof' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['number'] = $this->bookingService->generateCode('PAY');
        $data['status'] = PaymentStatus::Pending;

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('payments', 'public');
        }

        $payment = Payment::create($data);

        log_activity('payment.created', "Pembayaran {$payment->number} dicatat", $payment);

        return redirect()->route('app.payments.index')
            ->with('success', 'Pembayaran dicatat dan menunggu verifikasi.');
    }

    public function verify(Payment $payment)
    {
        $this->paymentService->verify($payment);

        return back()->with('success', 'Pembayaran diverifikasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:255']]);

        $this->paymentService->reject($payment, $request->reason);

        return back()->with('success', 'Pembayaran ditolak.');
    }
}
