<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Payment::with('invoice.booking.package')->latest('payment_date')->get();
    }

    public function headings(): array
    {
        return ['No. Pembayaran', 'Tanggal', 'Invoice', 'Paket', 'Jenis', 'Metode', 'Jumlah', 'Status'];
    }

    public function map($payment): array
    {
        return [
            $payment->number,
            optional($payment->payment_date)->format('d/m/Y'),
            $payment->invoice?->number,
            $payment->invoice?->booking?->package?->name,
            $payment->type?->label(),
            $payment->method,
            (float) $payment->amount,
            $payment->status?->label(),
        ];
    }
}
