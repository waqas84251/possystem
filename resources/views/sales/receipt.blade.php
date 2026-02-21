<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->sale_number }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛒</text></svg>">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .receipt { max-width: 300px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { font-weight: bold; border-top: 1px solid #000; padding-top: 10px; margin-top: 10px; }
        .text-center { text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>{{ $businessName }}</h2>
            <p>{{ $receiptHeader }}</p>
            <p>Sale #: {{ $sale->sale_number }}</p>
            <p>Date: {{ $sale->created_at->format('M d, Y h:i A') }}</p>
        </div>
        
        <div class="items">
    @foreach($sale->items as $item)
    <div class="item">
        <span>
            {{ $item->name }} <!-- This works for both manual and barcode products -->
            @if($item->is_manual)
                <span class="manual-badge">Manual</span>
            @endif
            (x{{ $item->quantity }})
        </span>
        <span>{{ $currencySymbol }}{{ number_format($item->total_price, 2) }}</span>
    </div>
    @endforeach
</div>
        
        <div class="total">
            <div class="item">
                <span>Total:</span>
                <span>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>
        
        <div class="footer text-center" style="margin-top: 20px;">
            <p>{{ $receiptFooter }}</p>
        </div>
        
        <div class="no-print text-center" style="margin-top: 30px;">
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>
</body>
</html>