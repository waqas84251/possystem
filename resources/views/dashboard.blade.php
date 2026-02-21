@extends('layouts.app')

@section('title', 'Dashboard - POS System')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div>
            <span class="badge bg-primary p-2">
                <i class="fas fa-calendar me-1"></i>
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Today's Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($todaySales, 2) }}</div>
                            <div class="mt-1 text-muted small">{{ $todayOrders }} orders</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Weekly Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($weeklySales, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Monthly Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($monthlySales, 2) }}</div>
                            <div class="mt-1 text-muted small">{{ $monthlyOrders }} orders</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCustomers }}</div>
                            <div class="mt-1 text-muted small">{{ $todayCustomers }} new today</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Overview (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods (Last 30 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($paymentMethods as $method)
                            <span class="mr-2">
                                <i class="fas fa-circle 
                                    @if($method->payment_method == 'cash') text-primary
                                    @elseif($method->payment_method == 'card') text-success
                                    @elseif($method->payment_method == 'transfer') text-info
                                    @endif"></i> 
                                {{ ucfirst($method->payment_method) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Sales -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sale #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_number }}</td>
                                    <td>{{ $sale->customer ? $sale->customer->name : 'Walk-in' }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>{{ $sale->created_at->format('M d, h:i A') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No recent sales</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">View All Sales</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->total_sold }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format($product->price * $product->total_sold, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No sales data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary">View All Products</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Low Stock Alert ({{ $lowStockProducts->count() }} products)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Alert Threshold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                <tr class="{{ $product->stock == 0 ? 'table-danger' : 'table-warning' }}">
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td class="font-weight-bold">{{ $product->stock }}</td>
                                    <td>{{ $lowStockThreshold }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('products.low-stock') }}" class="btn btn-sm btn-outline-warning">
                            View All Low Stock Items
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Sales Chart
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach($salesChartData as $data)
                    "{{ Carbon\Carbon::parse($data->date)->format('M j') }}",
                @endforeach
            ],
            datasets: [{
                label: 'Sales Amount',
                data: [
                    @foreach($salesChartData as $data)
                        {{ $data->total_amount }},
                    @endforeach
                ],
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '{{ $currencySymbol }}' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '{{ $currencySymbol }}' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Payment Methods Chart
    var ctx2 = document.getElementById('paymentMethodsChart').getContext('2d');
    var paymentMethodsChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($paymentMethods as $method)
                    "{{ ucfirst($method->payment_method) }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($paymentMethods as $method)
                        {{ $method->total }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($paymentMethods as $method)
                        @if($method->payment_method == 'cash') '#4e73df',
                        @elseif($method->payment_method == 'card') '#1cc88a',
                        @elseif($method->payment_method == 'transfer') '#36b9cc',
                        @else '#858796',
                        @endif
                    @endforeach
                ],
                hoverBackgroundColor: [
                    @foreach($paymentMethods as $method)
                        @if($method->payment_method == 'cash') '#2e59d9',
                        @elseif($method->payment_method == 'card') '#17a673',
                        @elseif($method->payment_method == 'transfer') '#2c9faf',
                        @else '#60616f',
                        @endif
                    @endforeach
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '{{ $currencySymbol }}' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection

@section('styles')
<style>
.card {
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
.chart-area {
    position: relative;
    height: 20rem;
}
.chart-pie {
    position: relative;
    height: 15rem;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection