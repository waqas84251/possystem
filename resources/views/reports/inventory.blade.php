@extends('layouts.app')

@section('title', 'Inventory Reports - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Inventory Reports</h1>
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
            <a href="{{ route('reports.customers') }}" class="btn btn-outline-primary">Customers</a>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Inventory Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('reports.inventory.export') }}" method="POST">
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
                                    Inventory Summary
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeCategories" name="sections[]" value="categories" checked>
                                <label class="form-check-label" for="includeCategories">
                                    Inventory by Category
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Columns to Include</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colCategory" name="columns[]" value="category" checked>
                                <label class="form-check-label" for="colCategory">
                                    Category
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colTotalStock" name="columns[]" value="total_stock" checked>
                                <label class="form-check-label" for="colTotalStock">
                                    Total Stock
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colTotalValue" name="columns[]" value="total_value" checked>
                                <label class="form-check-label" for="colTotalValue">
                                    Total Value
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colAvgValue" name="columns[]" value="avg_value" checked>
                                <label class="form-check-label" for="colAvgValue">
                                    Average Value
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

    <!-- Inventory Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inventorySummary->total_products }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stock Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($inventorySummary->total_value, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inventorySummary->low_stock }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inventorySummary->out_of_stock }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory by Category -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Inventory by Category</h5>
        </div>
        <div class="card-body">
            @if(count($inventoryByCategory) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Stock</th>
                            <th>Total Value</th>
                            <th>Average Value per Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryByCategory as $category)
                        <tr>
                            <td>{{ $category['name'] }}</td>
                            <td>{{ $category['total_stock'] }}</td>
                            <td class="fw-bold">{{ $currencySymbol }}{{ number_format($category['total_value'], 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($category['average_value'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td>{{ $inventorySummary->total_stock }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($inventorySummary->total_value, 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($inventorySummary->total_stock > 0 ? $inventorySummary->total_value / $inventorySummary->total_stock : 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Inventory Data</h4>
                <p class="text-muted">No inventory data available for categories.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection