<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\JamaahStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function verify(Payment $payment): void
    {
        if ($payment->status === PaymentStatus::Verified) {
            return;
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => PaymentStatus::Verified,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $this->recalculateInvoice($payment->invoice);

            log_activity('payment.verified', "Pembayaran {$payment->number} diverifikasi", $payment);
        });
    }

    public function recordCashierPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create([
                ...$data,
                'status' => PaymentStatus::Verified,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $this->recalculateInvoice($payment->invoice);

            log_activity('payment.cashier', "Pembayaran kasir {$payment->number} tercatat & terverifikasi", $payment);

            return $payment->fresh(['invoice.booking.package']);
        });
    }

    public function reject(Payment $payment, ?string $reason = null): void
    {
        DB::transaction(function () use ($payment, $reason) {
            $payment->update([
                'status' => PaymentStatus::Rejected,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'notes' => $reason,
            ]);

            $this->recalculateInvoice($payment->invoice);

            log_activity('payment.rejected', "Pembayaran {$payment->number} ditolak", $payment);
        });
    }

    public function recalculateInvoice(Invoice $invoice): void
    {
        $paid = $invoice->payments()
            ->where('status', PaymentStatus::Verified)
            ->sum('amount');

        $status = match (true) {
            $paid <= 0 => InvoiceStatus::Unpaid,
            $paid < (float) $invoice->total_amount => InvoiceStatus::Partial,
            default => InvoiceStatus::Paid,
        };

        $invoice->update([
            'paid_amount' => $paid,
            'status' => $status,
        ]);

        $this->syncJamaahStatus($invoice, $status);
    }

    protected function syncJamaahStatus(Invoice $invoice, InvoiceStatus $status): void
    {
        $jamaahStatus = match ($status) {
            InvoiceStatus::Paid => JamaahStatus::Lunas,
            InvoiceStatus::Partial => JamaahStatus::Cicilan,
            default => JamaahStatus::Booking,
        };

        $jamaahIds = $invoice->booking->jamaahPivot()->pluck('jamaah_id');

        \App\Models\Jamaah::whereIn('id', $jamaahIds)
            ->whereNotIn('status', [JamaahStatus::Berangkat->value, JamaahStatus::Selesai->value, JamaahStatus::Batal->value])
            ->update(['status' => $jamaahStatus]);
    }
}
