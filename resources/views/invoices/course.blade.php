<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
        .logo { font-size:20px; font-weight:700; }
        .muted { color:#6b7280; }
        .table { width:100%; border-collapse:collapse; margin-top:16px; }
        .table th, .table td { padding:8px 6px; border-bottom:1px solid #e5e7eb; text-align:left; }
        .text-right { text-align:right; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">LearnFlow</div>
            <div class="muted">learnflow.io</div>
        </div>
        <div style="text-align:right;">
            <div><strong>Invoice #{{ $order->id }}</strong></div>
            <div class="muted">Date: {{ $order->created_at->format('M j, Y') }}</div>
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <strong>Billed To:</strong><br>
        {{ $order->user->name }}<br>
        {{ $order->user->email }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Course: {{ $order->course->title }}</td>
                <td class="text-right">{{ strtoupper($order->currency) }} {{ number_format($order->amount, 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td class="text-right">{{ strtoupper($order->currency) }} 0.00</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td class="text-right"><strong>{{ strtoupper($order->currency) }} {{ number_format($order->amount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

