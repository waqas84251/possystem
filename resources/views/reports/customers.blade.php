@extends('layouts.app')

@section('title', 'Customers Reports - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Customers Reports</h1>
        <div>
            <!-- Export Dropdown -->
            <div class="btn-group me-2">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">CSV</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">Excel</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">PDF</a></li>
                </ul>
            </div>
            
            <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary me-2">Sales</a>
            <a href="{{ route('reports.products') }}" class="btn btn-outline-primary me-2">Products</a>
            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-primary">Inventory</a>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Customer Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('reports.customers.export') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="format" class="form-label">Format</label>
                            <select class="form-select" id="format" name="format" required>
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sections to Include</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeSummary" name="sections[]" value="summary" checked>
                                <label class="form-check-label" for="includeSummary">
                                    Customer Summary
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeTopCustomers" name="sections[]" value="top_customers" checked>
                                <label class="form-check-label" for="includeTopCustomers">
                                    Top Customers
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Columns to Include (Top Customers)</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colCustomerName" name="columns[]" value="customer_name" checked>
                                <label class="form-check-label" for="colCustomerName">
                                    Customer Name
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colEmail" name="columns[]" value="email" checked>
                                <label class="form-check-label" for="colEmail">
                                    Email
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colPhone" name="columns[]" value="phone" checked>
                                <label class="form-check-label" for="colPhone">
                                    Phone
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colTotalOrders" name="columns[]" value="total_orders" checked>
                                <label class="form-check-label" for="colTotalOrders">
                                    Total Orders
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colTotalSpent" name="columns[]" value="total_spent" checked>
                                <label class="form-check-label" for="colTotalSpent">
                                    Total Spent
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colAvgOrderValue" name="columns[]" value="avg_order_value" checked>
                                <label class="form-check-label" for="colAvgOrderValue">
                                    Avg. Order Value
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Customer Activity Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customerActivity->total_customers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customerActivity->active_customers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">New Customers (30d)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customerActivity->new_customers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg. Customer Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $currencySymbol }}{{ $customerActivity->total_customers > 0 ? number_format($topCustomers->sum('total_spent') / $customerActivity->total_customers, 2) : '0.00' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Top 10 Customers by Spending</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Avg. Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email ?? 'No email' }}</td>
                            <td>{{ $customer->phone ?? 'No phone' }}</td>
                            <td>{{ $customer->sales_count }}</td>
                            <td class="fw-bold text-success">{{ $currencySymbol }}{{ number_format($customer->total_spent, 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($customer->sales_count > 0 ? $customer->total_spent / $customer->sales_count : 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection