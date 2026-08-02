<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT #{{ $order->kot_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px;
        }
        .ticket {
            width: 80mm;
            background: #fff;
            padding: 12px 10px;
            font-size: 13px;
            line-height: 1.45;
        }
        .center { text-align: center; }
        .big { font-size: 20px; font-weight: bold; }
        .dashed { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; vertical-align: top; }
        .qty { width: 34px; font-weight: bold; font-size: 15px; }
        .toolbar { margin-bottom: 12px; display: flex; gap: 8px; }
        .toolbar a, .toolbar button {
            font-family: system-ui, sans-serif; font-size: 13px; font-weight: 700;
            padding: 9px 16px; border-radius: 10px; border: none; cursor: pointer; text-decoration: none;
        }
        .toolbar button { background: #2563eb; color: #fff; }
        .toolbar a { background: #fff; color: #475569; border: 1px solid #cbd5e1; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <a href="{{ route('admin.dining.orders.index') }}">&larr; Orders</a>
        <button onclick="window.print()">Print KOT</button>
    </div>

    <div class="ticket">
        <div class="center">
            <div style="font-weight:bold;">{{ site('hotel_name', 'Hotel') }}</div>
            <div>*** KITCHEN ORDER TICKET ***</div>
        </div>
        <div class="dashed"></div>
        <div class="center big">KOT #{{ $order->kot_number }}</div>
        <div class="dashed"></div>
        <table>
            <tr><td>Order</td><td style="text-align:right;">#{{ 5000 + $order->id }}</td></tr>
            <tr><td>Time</td><td style="text-align:right;">{{ $order->created_at->format('d M, h:i A') }}</td></tr>
            <tr>
                <td>Room</td>
                <td style="text-align:right; font-weight:bold;">
                    {{ $order->booking->assignedRooms->pluck('room_number')->join(', ') ?: 'Unassigned' }}
                </td>
            </tr>
            <tr><td>Guest</td><td style="text-align:right;">{{ $order->booking->guests->first()->name ?? '—' }}</td></tr>
        </table>
        <div class="dashed"></div>
        <table>
            <tr style="border-bottom:1px solid #000;">
                <td class="qty">QTY</td>
                <td style="font-weight:bold;">ITEM</td>
            </tr>
            @foreach($order->items ?? [] as $item)
                <tr>
                    <td class="qty">{{ $item['qty'] ?? 1 }}x</td>
                    <td>{{ $item['name'] ?? 'Item' }}</td>
                </tr>
            @endforeach
        </table>
        @if($order->notes)
            <div class="dashed"></div>
            <div><strong>NOTE:</strong> {{ $order->notes }}</div>
        @endif
        <div class="dashed"></div>
        <div class="center">-- Not a bill. Kitchen copy. --</div>
    </div>

    <script>
        // Auto-open print dialog when opened with ?print=1
        if (new URLSearchParams(location.search).get('print') === '1') {
            window.addEventListener('load', () => setTimeout(() => window.print(), 150));
        }
    </script>
</body>

</html>
