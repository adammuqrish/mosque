<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Akad Zakat</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a2e; margin: 40px; }
        .border-box { border: 2px solid #C5A059; padding: 30px; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px solid #C5A059; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { color: #C5A059; font-size: 22px; margin: 0 0 5px 0; }
        .header h2 { font-size: 18px; margin: 0 0 5px 0; color: #0B6E4F; }
        .header p { font-size: 11px; color: #666; margin: 0; }
        .bismillah { text-align: center; font-size: 18px; margin-bottom: 20px; color: #C5A059; }
        .ref { text-align: right; font-size: 11px; color: #666; margin-bottom: 20px; }
        .details { width: 100%; }
        .details td { padding: 8px 5px; vertical-align: top; }
        .details .label { font-weight: bold; color: #555; width: 120px; }
        .details .value { color: #1a1a2e; }
        .details .divider { border-top: 1px dashed #ddd; }
        .amount { font-size: 16px; font-weight: bold; color: #0B6E4F; }
        .signature { margin-top: 40px; text-align: right; }
        .signature .line { margin-top: 50px; border-top: 1px solid #333; width: 250px; display: inline-block; padding-top: 5px; font-size: 11px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        .stamp { margin-top: -30px; text-align: left; }
        .stamp .box { border: 2px solid #C5A059; border-radius: 4px; display: inline-block; padding: 8px 15px; font-size: 10px; color: #C5A059; font-weight: bold; text-align: center; }
        .note { background: #f9f9f9; padding: 10px; border-radius: 4px; font-size: 10px; color: #666; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="border-box">
        <div class="bismillah">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>

        <div class="header">
            <h1>SURAT AKAD ZAKAT</h1>
            <h2>Masjid Al-Mukminun</h2>
            <p>Sistem Pengurusan Masjid — Smart Mosque Platform</p>
        </div>

        <div class="ref">
            No. Akad: <strong>{{ $akad->akad_reference }}</strong>
        </div>

        <table class="details">
            <tr>
                <td class="label">Tarikh Akad</td>
                <td class="value">: {{ $akad->akad_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jenis</td>
                <td class="value">: {{ $donation->category === 'zakat_fitr' ? 'Zakat Fitr' : 'Zakat (Wajib)' }}</td>
            </tr>
            <tr><td colspan="2" class="divider"></td></tr>
            <tr>
                <td class="label">Muzakki (Pembayar)</td>
                <td class="value">: <strong>{{ $akad->muzakki_name }}</strong></td>
            </tr>
            @if($akad->muzakki_ic)
            <tr>
                <td class="label">No. IC</td>
                <td class="value">: {{ $akad->muzakki_ic }}</td>
            </tr>
            @endif
            <tr><td colspan="2" class="divider"></td></tr>
            <tr>
                <td class="label">Jumlah Zakat</td>
                <td class="value amount">: RM {{ number_format($akad->amount, 2) }}</td>
            </tr>
            <tr><td colspan="2" class="divider"></td></tr>
            <tr>
                <td class="label">Amil (Penerima)</td>
                <td class="value">: <strong>{{ $akad->amil_display }}</strong></td>
            </tr>
            @if($akad->notes)
            <tr>
                <td class="label">Catatan</td>
                <td class="value">: {{ $akad->notes }}</td>
            </tr>
            @endif
        </table>

        @if($akad->notes)
        <div class="note">
            <strong>Catatan:</strong> {{ $akad->notes }}
        </div>
        @endif

        <div style="margin-top: 25px; padding: 10px; background: #FDF6E3; border-radius: 4px; font-size: 10px; color: #8B7355; text-align: center;">
            <em>"Ambillah zakat dari harta mereka, dengan zakat itu kamu membersihkan dan mensucikan mereka."</em><br>
            — (At-Tawbah: 103)
        </div>

        <div class="signature">
            <div class="line">
                Tandatangan Amil<br>
                {{ $akad->amil_display }}
            </div>
        </div>

        <div class="stamp">
            <div class="box">
                MASJID<br>AL-MUKMINUN
            </div>
        </div>

        <div class="footer">
            Dokumen ini sah dan diterbitkan oleh Sistem Pengurusan Masjid Al-Mukminun.<br>
            Generated on {{ now()->format('d F Y, h:i A') }}
        </div>
    </div>
</body>
</html>
