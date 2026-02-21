<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin-bottom: 5px; }
        .header p { margin: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .section-header { background-color: #e9ecef; padding: 10px; margin-top: 20px; margin-bottom: 10px; font-weight: bold; }
        .summary { margin-bottom: 20px; }
        .summary-item { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Products Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>
    
    @if(in_array('top_products', $sections))
    <div class="section-header">TOP SELLING PRODUCTS</div>
    <table>
        <thead>
            <tr>
                @if(in_array('product_name', $columns))<th>Product Name</th>@endif
                @if(in_array('category', $columns))<th>Category</th>@endif
                @if(in_array('units_sold', $columns))<th>Units Sold</th>@endif
                @if(in_array('revenue', $columns))<th>Revenue</th>@endif
                @if(in_array('stock', $columns))<th>Stock</th>@endif
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $product)
            <tr>
                @if(in_array('product_name', $columns))<td>{{ $product->name }}</td>@endif
                @if(in_array('category', $columns))<td>{{ $product->category->name }}</td>@endif
                @if(in_array('units_sold', $columns))<td>{{ $product->total_sold }}</td>@endif
                @if(in_array('revenue', $columns))<td>{{ $currencySymbol }}{{ number_format($product->total_revenue, 2) }}</td>@endif
                @if(in_array('stock', $columns))<td>{{ $product->stock }}</td>@endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    @if(in_array('low_stock', $sections) && $lowStockProducts->count() > 0)
    <div class="section-header">LOW STOCK PRODUCTS</div>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStockProducts as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $currencySymbol }}{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock == 0 ? 'Out of Stock' : 'Low Stock' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    @if(in_array('categories', $sections))
    <div class="section-header">PRODUCTS BY CATEGORY</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Number of Products</th>
                <th>Total Stock</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productsByCategory as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->products_count }}</td>
                <td>{{ $category->total_stock }}</td>
                <td>{{ $currencySymbol }}{{ number_format($category->total_value, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>