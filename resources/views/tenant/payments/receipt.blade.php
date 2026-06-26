<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #1e293b; margin: 0; }
        .header { border-bottom: 3px solid #0d9488; padding-bottom: 12px; margin-bottom: 20px; }
        .brand { font-size: 20px; font-weight: bold; color: #0d9488; }
        .muted { color: #64748b; font-size: 11px; }
        .title { font-size: 24px; font-weight: bold; text-align: right; color: #334155; }
        table { width: 100%; border-collapse: collapse; }
        .items th { background: #f1f5f9; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; color: #475569; }
        .items td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .amount-box { margin: 20px 0; padding: 16px; border: 2px solid #0d9488; border-radius: 8px; background: #f0fdfa; }
        .amount { font-size: 18px; font-weight: bold; color: #0d9488; }
        .terbilang { font-style: italic; color: #475569; margin-top: 6px; }
        .signature { margin-top: 40px; }
        .signature td { width: 50%; text-align: center; vertical-align: top; }
        .sign-line { margin-top: 60px; border-top: 1px solid #94a3b8; padding-top: 6px; font-size: 11px; }
        .footer { margin-top: 24px; font-size: 10px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 60%;">
                <div class="brand">{{ $tenant->name ?? config('app.name') }}</div>
                <div class="muted">
                    {{ $tenant->address ?? '' }} {{ $tenant->city ? ', ' . $tenant->city : '' }}<br>
                    {{ $tenant->phone ?? '' }} @if($tenant->email) &middot; {{ $tenant->email }} @endif<br>
                    @if($tenant->izin_number) {{ $tenant->izin_number }} @endif
                </div>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <div class="title">KWITANSI</div>
                <div class="muted right">
                    No: {{ $payment->number }}<br>
                    Tanggal: {{ tanggal_id($payment->payment_date) }}<br>
                    Invoice: {{ $invoice->number }}
                </div>
            </td>
        </tr>
    </table>

    <p>Telah diterima pembayaran dari:</p>

    <table class="items" style="margin-bottom: 16px;">
        <tr>
            <td style="width: 30%;" class="muted">Nama Jamaah</td>
            <td><strong>
                {{ $invoice->booking?->jamaahPivot->map(fn ($bj) => $bj->jamaah->full_name)->join(', ') ?: '-' }}
            </strong></td>
        </tr>
        <tr>
            <td class="muted">Paket</td>
            <td><strong>{{ $invoice->booking?->package?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="muted">Jenis Pembayaran</td>
            <td>{{ $payment->type->label() }}</td>
        </tr>
        <tr>
            <td class="muted">Metode</td>
            <td>{{ ucfirst($payment->method) }}@if($payment->bank_name) &mdash; {{ $payment->bank_name }} @endif</td>
        </tr>
        @if($payment->notes)
            <tr>
                <td class="muted">Catatan</td>
                <td>{{ $payment->notes }}</td>
            </tr>
        @endif
    </table>

    <div class="amount-box">
        <div class="muted">Jumlah Diterima</div>
        <div class="amount">{{ rupiah($payment->amount) }}</div>
        <div class="terbilang">Terbilang: {{ ucfirst(terbilang($payment->amount)) }}</div>
    </div>

    <table class="items">
        <tr>
            <td class="muted">Total Tagihan Invoice</td>
            <td class="right">{{ rupiah($invoice->total_amount) }}</td>
        </tr>
        <tr>
            <td class="muted">Total Terbayar (setelah transaksi ini)</td>
            <td class="right">{{ rupiah($invoice->paid_amount) }}</td>
        </tr>
        <tr>
            <td class="muted"><strong>Sisa Tagihan</strong></td>
            <td class="right"><strong>{{ rupiah($invoice->outstanding()) }}</strong></td>
        </tr>
    </table>

    <table class="signature">
        <tr>
            <td>
                <div class="muted">Penerima,</div>
                <div class="sign-line">{{ $payment->verifier?->name ?? '________________' }}</div>
            </td>
            <td>
                <div class="muted">Penyetor,</div>
                <div class="sign-line">________________</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Kwitansi ini sah dan dicetak dari sistem pada {{ tanggal_id(now(), true) }}.
        @if($payment->verified_at)
            Diverifikasi: {{ tanggal_id($payment->verified_at) }}.
        @endif
    </div>
</body>
</html>
