@extends('layouts.app')

@section('title', 'Sales Reports - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sales Reports</h1>
        <div class="d-flex align-items-center">
            <!-- Report Navigation Buttons -->
            <a href="{{ route('reports.products') }}" class="btn btn-outline-primary me-2">Products</a>
            <a href="{{ route('reports.customers') }}" class="btn btn-outline-primary me-2">Customers</a>
            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-primary me-3">Inventory</a>

            <!-- Export Dropdown -->
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                            Export Options
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('reports.sales.export', ['format' => 'csv', 'date_range' => $dateRange, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}">
                            CSV Format
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('reports.sales.export', ['format' => 'excel', 'date_range' => $dateRange, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}">
                            Excel Format
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('reports.sales.export', ['format' => 'pdf', 'date_range' => $dateRange, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}">
                            PDF Format
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
    <!-- Date Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <select class="form-select" name="date_range" onchange="this.form.submit()">
                        <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                
                @if($dateRange == 'custom')
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                </div>
                @else
                <div class="col-md-3">
                    <label class="form-label">Date Display</label>
                    <div class="form-control bg-light">
                        {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSales }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Sale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($averageSale, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Date Range</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Daily Sales Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Sales Summary</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Transactions</span>
                            <span class="badge bg-primary rounded-pill">{{ $totalSales }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Revenue</span>
                            <span class="badge bg-success rounded-pill">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Average Sale Value</span>
                            <span class="badge bg-info rounded-pill">{{ $currencySymbol }}{{ number_format($averageSale, 2) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Date Range</span>
                            <span class="badge bg-warning rounded-pill">
                                {{ $startDate->format('M d') }}-{{ $endDate->format('M d') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Sales Transactions</h5>
        </div>
        <div class="card-body">
            @if($sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Sale #</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="text-primary">
                                    {{ $sale->sale_number }}
                                </a>
                            </td>
                            <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $sale->customer ? $sale->customer->name : 'Walk-in' }}</td>
                            <td>{{ $sale->items->sum('quantity') }} items</td>
                            <td>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-info text-capitalize">{{ $sale->payment_method }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Sales Found</h4>
                <p class="text-muted">No sales transactions found for the selected date range.</p>
            </div>
            @endif
        </div>
    </div>
</div>
<!-- Export Options Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reports.sales.export') }}" method="GET" id="exportForm">
                    <input type="hidden" name="date_range" value="{{ $dateRange }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exportColumns" class="form-label">Columns to Include</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colSaleNumber" name="columns[]" value="sale_number" checked>
                            <label class="form-check-label" for="colSaleNumber">Sale Number</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colDate" name="columns[]" value="date" checked>
                            <label class="form-check-label" for="colDate">Date & Time</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colCustomer" name="columns[]" value="customer" checked>
                            <label class="form-check-label" for="colCustomer">Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colItems" name="columns[]" value="items" checked>
                            <label class="form-check-label" for="colItems">Items</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colAmount" name="columns[]" value="amount" checked>
                            <label class="form-check-label" for="colAmount">Total Amount</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="colPayment" name="columns[]" value="payment" checked>
                            <label class="form-check-label" for="colPayment">Payment Method</label>
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
        // Your existing chart code
        
        // Simple function to select all columns
        document.getElementById('selectAllColumns').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="columns[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesChart = document.getElementById('salesChart');
        const dailyData = @json($dailySales);

        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: dailyData.map(item => new Date(item.date).toLocaleDateString()),
                datasets: [{
                    label: 'Daily Revenue',
                    data: dailyData.map(item => item.total_amount),
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    fill: true,
                    tension: 0.3
                }, {
                    label: 'Number of Sales',
                    data: dailyData.map(item => item.sales_count),
                    borderColor: '#4cc9f0',
                    backgroundColor: 'rgba(76, 201, 240, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales Performance'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount / Count'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
@endsection