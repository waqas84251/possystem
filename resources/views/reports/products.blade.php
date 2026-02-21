@extends('layouts.app')

@section('title', 'Products Reports - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Products Reports</h1>
        <div>
            <!-- Export Dropdown -->
            <div class="btn-group me-2">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">Export Options</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('reports.products.export', ['format' => 'csv']) }}">CSV Format</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.products.export', ['format' => 'excel']) }}">Excel Format</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.products.export', ['format' => 'pdf']) }}">PDF Format</a></li>
                </ul>
            </div>
            
            <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary me-2">Sales</a>
            <a href="{{ route('reports.customers') }}" class="btn btn-outline-primary me-2">Customers</a>
            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-primary">Inventory</a>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Top 10 Selling Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Units Sold</th>
                                    <th>Total Revenue</th>
                                    <th>Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->total_sold }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format($product->total_revenue, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Products by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
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
                            <td class="fw-bold text-danger">{{ $product->stock }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($product->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning' }}">
                                    {{ $product->stock == 0 ? 'Out of Stock' : 'Low Stock' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Export Options Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Products Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reports.products.export') }}" method="GET" id="exportForm">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exportSection" class="form-label">Sections to Include</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sectionTopProducts" name="sections[]" value="top_products" checked>
                            <label class="form-check-label" for="sectionTopProducts">Top Selling Products</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sectionLowStock" name="sections[]" value="low_stock" checked>
                            <label class="form-check-label" for="sectionLowStock">Low Stock Products</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sectionCategories" name="sections[]" value="categories" checked>
                            <label class="form-check-label" for="sectionCategories">Products by Category</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Columns for Top Products</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colProductName" name="columns[]" value="product_name" checked>
                            <label class="form-check-label" for="colProductName">Product Name</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colCategory" name="columns[]" value="category" checked>
                            <label class="form-check-label" for="colCategory">Category</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colUnitsSold" name="columns[]" value="units_sold" checked>
                            <label class="form-check-label" for="colUnitsSold">Units Sold</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colRevenue" name="columns[]" value="revenue" checked>
                            <label class="form-check-label" for="colRevenue">Revenue</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colStock" name="columns[]" value="stock" checked>
                            <label class="form-check-label" for="colStock">Stock</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="exportForm" class="btn btn-primary">Export</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category Chart
        const categoryChart = document.getElementById('categoryChart');
        const categoryData = @json($productsByCategory);

        new Chart(categoryChart, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.products_count),
                    backgroundColor: [
                        '#4361ee', '#4cc9f0', '#f72585', '#7209b7', '#3a0ca3',
                        '#4cc9f0', '#f72585', '#7209b7', '#3a0ca3', '#4361ee'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
@endsection