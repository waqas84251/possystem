@extends('layouts.app')

@section('title', 'Inventory Management - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Inventory Management</h1>
        @auth
        <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning">
            <i class="fas fa-exclamation-triangle me-1"></i> Low Stock Alerts
        </a>
        @endauth
    </div>

    <!-- Inventory Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Inventory Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }}{{ number_format($totalValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Items in Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalItems) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStockCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All Products Inventory</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Price</th>
                            <th>Stock Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                            <td>
                                <span class="fw-bold">{{ $product->stock }}</span>
                            </td>
                            <td>{{ $currencySymbol }}{{ number_format($product->price, 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($product->price * $product->stock, 2) }}</td>
                            <td>
                                @if($product->stock == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->stock < 5)
                                    <span class="badge bg-warning">Low Stock</span>
                                @elseif($product->stock < 10)
                                    <span class="badge bg-info">Medium Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @auth
                                    <a href="{{ route('inventory.show-adjust', $product) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Adjust Stock">
                                        <i class="fas fa-edit"></i> Adjust
                                    </a>
                                    @endauth
                                    <a href="{{ route('inventory.stock-history', $product) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View History">
                                        <i class="fas fa-history"></i> View History
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection