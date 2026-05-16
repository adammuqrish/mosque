<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #1a1a1a; }
        .certificate { width: 100%; padding: 40px; border: 3px solid #0d9488; position: relative; }
        .inner-border { border: 1px solid #0d9488; padding: 30px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 28px; color: #0d9488; letter-spacing: 3px; text-transform: uppercase; }
        .header .subtitle { font-size: 14px; color: #666; margin-top: 5px; }
        .bismillah { text-align: center; font-size: 18px; margin: 15px 0; color: #333; }
        .quran-verse { text-align: center; font-style: italic; font-size: 12px; color: #555; margin: 10px 0 20px; padding: 10px; background: #f0fdfa; }
        .content { text-align: center; margin: 20px 0; }
        .content p { font-size: 14px; margin: 8px 0; }
        .content .name { font-size: 26px; font-weight: bold; color: #0d9488; margin: 15px 0; border-bottom: 2px solid #0d9488; display: inline-block; padding-bottom: 5px; }
        .content .reward { font-size: 16px; font-weight: bold; color: #333; margin: 10px 0; }
        .footer { display: flex; justify-content: space-between; margin-top: 40px; padding-top: 20px; }
        .footer .signature { text-align: center; width: 45%; }
        .footer .signature .line { border-top: 1px solid #333; margin: 40px auto 5px; width: 80%; }
        .footer .signature p { font-size: 12px; color: #666; }
        .date { text-align: right; font-size: 12px; color: #666; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border">
            <div class="header">
                <h1>Certificate of Appreciation</h1>
                <p class="subtitle">{{ config('app.name') }}</p>
            </div>

            <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>

            <div class="quran-verse">
                "Indeed, the most noble of you in the sight of Allah is the most righteous of you." — Surah Al-Hujurat (49:13)
            </div>

            <div class="content">
                <p>This certificate is proudly presented to</p>
                <div class="name">{{ strtoupper($user->name) }}</div>
                <p>in recognition of your dedicated volunteer service and contributions to our mosque community.</p>
                <p class="reward">Redeemed: {{ $reward->name }}</p>
                @if($tier)
                    <p>Current Tier: <strong>{{ $tier->name }}</strong></p>
                @endif
            </div>

            <div class="footer">
                <div class="signature">
                    <div class="line"></div>
                    <p>Mosque Committee Chairman</p>
                </div>
                <div class="signature">
                    <div class="line"></div>
                    <p>Volunteer Coordinator</p>
                </div>
            </div>

            <div class="date">Issued: {{ $date }}</div>
        </div>
    </div>
</body>
</html>
