<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h1 { color: #1f2937; font-size: 24px; margin-bottom: 10px; }
        .summary, .monthly { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .summary th, .summary td, .monthly th, .monthly td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        .summary th, .monthly th { background-color: #2563eb; color: #fff; }
        .monthly th { font-size: 12px; }
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Financial Summary Report</h1>
    @if(!empty($period))
        <p>Report Period: {{ $period }}</p>
    @endif
    <p>Generated at: {{ $generatedAt }}</p>

    <table class="summary">
        <thead>
            <tr>
                <th>Label</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $row)
                <tr>
                    <td>{{ $row['Label'] }}</td>
                    <td>{{ $row['Value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="monthly">
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Donations</th>
                <th>Total Withdrawals</th>
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
                    <td colspan="3" style="text-align:center;color:#6b7280;">No monthly data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Mosque Management System - Automatically generated report</p>
    </div>
</body>
</html>
