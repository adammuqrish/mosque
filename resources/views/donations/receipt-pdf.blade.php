<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Donation Receipt</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a2e; margin: 40px; }
        .border-box { border: 2px solid #0B6E4F; padding: 30px; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px solid #0B6E4F; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { color: #0B6E4F; font-size: 22px; margin: 0 0 5px 0; }
        .header h2 { font-size: 18px; margin: 0 0 5px 0; color: #333; }
        .header p { font-size: 11px; color: #666; margin: 0; }
        .receipt-label { text-align: center; font-size: 14px; font-weight: bold; color: #0B6E4F; margin: 15px 0; padding: 8px; background: #F0FFF4; border-radius: 4px; }
        .ref { text-align: right; font-size: 11px; color: #666; margin-bottom: 20px; }
        .details { width: 100%; }
        .details td { padding: 8px 5px; vertical-align: top; }
        .details .label { font-weight: bold; color: #555; width: 140px; }
        .details .value { color: #1a1a2e; }
        .details .divider { border-top: 1px dashed #ddd; }
        .amount-box { text-align: center; margin: 25px 0; padding: 15px; background: #F0FFF4; border: 2px solid #0B6E4F; border-radius: 8px; }
        .amount-label { font-size: 11px; color: #666; margin-bottom: 5px; }
        .amount { font-size: 24px; font-weight: bold; color: #0B6E4F; }
        .category-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; color: white; margin-top: 8px; }
        .zakat { background: #C5A059; }
        .sadaqah { background: #0B6E4F; }
        .waqf { background: #7C3AED; }
        .zakat-fitr { background: #D97706; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        .disclaimer { background: #F9F9F9; padding: 10px; border-radius: 4px; font-size: 10px; color: #666; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="border-box">
        <div class="header">
            <h1>MASJID AL-MUKMINUN</h1>
            <h2>Donation Receipt</h2>
            <p>Sistem Pengurusan Masjid — Smart Mosque Platform</p>
        </div>

        <div class="receipt-label">RESIPT SUMBANGAN / DONATION RECEIPT</div>

        <div class="ref">
            Receipt No: <strong>{{ $donation->receipt_number }}</strong>
        </div>

        <table class="details">
            <tr>
                <td class="label">Tarikh / Date</td>
                <td class="value">: {{ $donation->donation_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jenis / Category</td>
                <td class="value">: <span class="category-badge {{ $donation->category }}">{{ $donation->shariah_type_label }}</span></td>
            </tr>
            <tr>
                <td class="label">Tujuan Dana / Fund Purpose</td>
                <td class="value">: {{ $donation->fund_purpose_label ?? '-' }}</td>
            </tr>
            <tr><td colspan="2" class="divider"></td></tr>
            <tr>
                <td class="label">Nama Donor / Donor Name</td>
                <td class="value">: <strong>{{ $donation->donor_display_name }}</strong></td>
            </tr>
            @if($donation->donor_ic)
            <tr>
                <td class="label">No. IC / IC Number</td>
                <td class="value">: {{ $donation->donor_display_ic }}</td>
            </tr>
            @endif
            <tr><td colspan="2" class="divider"></td></tr>
            <tr>
                <td class="label">Kaedah Bayar / Payment</td>
                <td class="value">: {{ ucfirst($donation->source) }}</td>
            </tr>
            @if($donation->reference)
            <tr>
                <td class="label">Rujukan / Reference</td>
                <td class="value">: {{ $donation->reference }}</td>
            </tr>
            @endif
        </table>

        <div class="amount-box">
            <div class="amount-label">JUMLAH / TOTAL AMOUNT</div>
            <div class="amount">RM {{ number_format($donation->amount, 2) }}</div>
        </div>

        <div class="disclaimer">
            <strong>Nota:</strong> Resit ini dikeluarkan untuk tujuan rekod sumbangan sahaja.<br>
            This receipt is issued for donation record purposes only.
        </div>

        <div class="footer">
            Dokumen ini sah dan diterbitkan oleh Sistem Pengurusan Masjid Al-Mukminun.<br>
            Generated on {{ now()->format('d F Y, h:i A') }}
        </div>
    </div>
</body>
</html>
