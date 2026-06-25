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
        .totals td { padding: 5px 8px; }
        .grand { font-size: 14px; font-weight: bold; color: #0d9488; }
        .footer { margin-top: 30px; font-size: 11px; color: #64748b; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 10px; background: #ccfbf1; color: #0f766e; }
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
                <div class="title">INVOICE</div>
                <div class="muted right">
                    No: {{ $invoice->number }}<br>
                    Tanggal: {{ tanggal_id($invoice->issued_date) }}<br>
                    Jatuh Tempo: {{ tanggal_id($invoice->due_date) }}
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-bottom: 16px;">
        <tr>
            <td style="width: 50%;">
                <div class="muted">Paket</div>
                <strong>{{ $invoice->booking?->package?->name }}</strong>
            </td>
            <td style="width: 50%;">
                <div class="muted">Status</div>
                <span class="badge">{{ $invoice->status->label() }}</span>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr><th>Nama Jamaah</th><th>Tipe Kamar</th><th class="right">Harga</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->booking->jamaahPivot as $bj)
                <tr>
                    <td>{{ $bj->jamaah->full_name }}</td>
                    <td>{{ $bj->room_type->label() }}</td>
                    <td class="right">{{ rupiah($bj->price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals" style="margin-top: 12px;">
        <tr><td class="right" style="width: 80%;">Subtotal</td><td class="right">{{ rupiah($invoice->total_amount) }}</td></tr>
        <tr><td class="right">Terbayar</td><td class="right">{{ rupiah($invoice->paid_amount) }}</td></tr>
        <tr><td class="right grand">Sisa Tagihan</td><td class="right grand">{{ rupiah($invoice->outstanding()) }}</td></tr>
    </table>

    @if($tenant->bank_name)
        <div class="footer">
            <strong>Pembayaran dapat ditransfer ke:</strong><br>
            {{ $tenant->bank_name }} - {{ $tenant->bank_account_number }} a.n. {{ $tenant->bank_account_holder }}
        </div>
    @endif

    <div class="footer">Terima kasih atas kepercayaan Anda. Invoice ini sah dan diproses oleh sistem.</div>
</body>
</html>
