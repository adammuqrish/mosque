<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Summary Report — Smart Mosque System</title>
    <style>
        @page {
            margin: 30mm 20mm 25mm 20mm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Sans', 'Amiri', 'Arial', sans-serif;
            color: #374151;
            font-size: 11px;
            line-height: 1.6;
        }

        /* ── Letterhead Header ── */
        .header {
            text-align: center;
            margin-bottom: 8mm;
            padding-bottom: 5mm;
            border-bottom: 2px solid #065f46;
        }
        .header .bismillah {
            font-family: 'Amiri', 'DejaVu Sans', serif;
            font-size: 18px;
            color: #065f46;
            direction: rtl;
            margin-bottom: 2px;
        }
        .header .mosque-name {
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
            letter-spacing: 0.5px;
        }
        .header .tagline {
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        /* ── Report Title Area ── */
        .report-title {
            text-align: center;
            margin: 6mm 0 4mm;
        }
        .report-title h1 {
            color: #065f46;
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 3px;
        }
        .report-title .meta {
            font-size: 9px;
            color: #6b7280;
        }

        /* ── Summary Table ── */
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
        }
        .summary th {
            background-color: #065f46;
            color: #ffffff;
            padding: 6px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary td {
            border: 1px solid #d1d5db;
            padding: 5px 10px;
            font-size: 10px;
        }
        .summary tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .summary .label {
            font-weight: 600;
            color: #374151;
            width: 40%;
        }
        .summary .value {
            font-weight: bold;
            color: #065f46;
        }

        /* ── Monthly Table ── */
        .monthly {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
        }
        .monthly th {
            background-color: #065f46;
            color: #ffffff;
            padding: 5px 8px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .monthly td {
            border: 1px solid #d1d5db;
            padding: 4px 8px;
            font-size: 9px;
        }
        .monthly tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 3mm;
        }
        .footer .page-number:before {
            content: "Page ";
        }
        .footer .page-number:after {
            content: " of " counter(totalPages);
        }

        .footer .brand {
            color: #065f46;
            font-weight: 600;
        }
    </style>
</head>
<body>

    {{-- Letterhead Header --}}
    <div class="header">
        <div class="bismillah">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
        <div class="mosque-name">Smart Mosque System</div>
        <div class="tagline">Laporan Kewangan &bull; Financial Report</div>
    </div>

    {{-- Report Title --}}
    <div class="report-title">
        <h1>Financial Summary Report</h1>
        <div class="meta">
            @if(!empty($period))
                Period: {{ $period }} &bull;
            @endif
            Generated: {{ $generatedAt }}
        </div>
    </div>

    {{-- Summary Table --}}
    @if(count($summary) > 0)
    <table class="summary">
        <thead>
            <tr>
                <th>Item</th>
                <th>Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $row)
                <tr>
                    <td class="label">{{ $row['Label'] }}</td>
                    <td class="value">{{ $row['Value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Monthly Breakdown --}}
    @if(count($monthly) > 0)
    <table class="monthly">
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Donations (RM)</th>
                <th>Total Withdrawals (RM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthly as $row)
                <tr>
                    <td>{{ $row['Month'] }}</td>
                    <td>{{ $row['Donations'] }}</td>
                    <td>{{ $row['Withdrawals'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align:center;color:#9ca3af;">No monthly data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span class="brand">Smart Mosque System</span>
        &mdash; Laporan ini dijana secara automatik &bull; Automatically generated report
        &mdash; <span class="page-number"></span>
    </div>

</body>
</html>
