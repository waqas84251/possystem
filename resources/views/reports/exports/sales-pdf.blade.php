<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin-bottom: 5px; }
        .header p { margin: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .summary { margin-bottom: 20px; }
        .summary-item { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-item"><strong>Total Sales:</strong> {{ $totalSales }}</div>
        <div class="summary-item"><strong>Total Revenue:</strong> {{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</div>
        <div class="summary-item"><strong>Generated On:</strong> {{ now()->format('M d, Y h:i A') }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                @if(in_array('sale_number', $columns))<th>Sale #</th>@endif
                @if(in_array('date', $columns))<th>Date & Time</th>@endif
                @if(in_array('customer', $columns))<th>Customer</th>@endif
                @if(in_array('items', $columns))<th>Items</th>@endif
                @if(in_array('amount', $columns))<th>Total Amount</th>@endif
                @if(in_array('payment', $columns))<th>Payment Method</th>@endif
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                @if(in_array('sale_number', $columns))<td>{{ $sale->sale_number }}</td>@endif
                @if(in_array('date', $columns))<td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>@endif
                @if(in_array('customer', $columns))<td>{{ $sale->customer ? $sale->customer->name : 'Walk-in' }}</td>@endif
                @if(in_array('items', $columns))<td>{{ $sale->items->sum('quantity') }} items</td>@endif
                @if(in_array('amount', $columns))<td>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>@endif
                @if(in_array('payment', $columns))<td>{{ $sale->payment_method }}</td>@endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>