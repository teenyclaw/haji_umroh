<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function cashier(Request $request)
    {
        $invoices = Invoice::with(['booking.package', 'booking.jamaahPivot.jamaah'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->latest()
            ->get()
            ->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'package' => $invoice->booking?->package?->name,
                'jamaah' => $invoice->booking?->jamaahPivot
                    ->map(fn ($bj) => $bj->jamaah->full_name)
                    ->join(', '),
                'total' => (float) $invoice->total_amount,
                'paid' => (float) $invoice->paid_amount,
                'outstanding' => $invoice->outstanding(),
            ]);

        return view('tenant.payments.cashier', [
            'invoices' => $invoices,
            'types' => PaymentType::options(),
            'selectedInvoice' => $request->integer('invoice_id') ?: null,
        ]);
    }

    public function cashierStore(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'type' => ['required', 'in:dp,cicilan,pelunasan'],
            'method' => ['required', 'in:tunai,qris'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);
        $outstanding = $invoice->outstanding();

        if ((float) $data['amount'] > $outstanding) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Jumlah melebihi sisa tagihan (' . rupiah($outstanding) . ').']);
        }

        $data['number'] = $this->bookingService->generateCode('PAY');
        $data['notes'] = $data['notes'] ?? 'Pembayaran via kasir';

        $payment = $this->paymentService->recordCashierPayment($data);

        return redirect()->route('app.payments.cashier.success', $payment);
    }

    public function cashierSuccess(Payment $payment)
    {
        abort_unless($payment->status === PaymentStatus::Verified, 404);

        $payment->load([
            'invoice.booking.package',
            'invoice.booking.jamaahPivot.jamaah',
        ]);

        return view('tenant.payments.cashier-success', compact('payment'));
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

    public function receipt(Payment $payment)
    {
        abort_unless($payment->status === PaymentStatus::Verified, 404);

        $payment->load([
            'invoice.booking.package',
            'invoice.booking.jamaahPivot.jamaah',
            'verifier',
        ]);

        $pdf = Pdf::loadView('tenant.payments.receipt', [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'tenant' => current_tenant(),
        ]);

        return $pdf->download("Kwitansi-{$payment->number}.pdf");
    }
}
