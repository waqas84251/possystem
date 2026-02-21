@extends('layouts.app')

@section('title', 'Low Stock Alerts - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts
        </h1>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>{{ $lowStockProducts->count() }} product(s) are low on stock!</strong> 
        Please consider restocking these items.
    </div>

    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5 class="card-title mb-0">Low Stock Products</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Minimum Recommended</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                <span class="fw-bold text-danger">{{ $product->stock }}</span>
                            </td>
                            <td>10 units</td>
                            <td>
                                @if($product->stock == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inventory.show-adjust', $product) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> Restock
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h3 class="text-success">All products are well stocked!</h3>
            <p class="text-muted">No low stock alerts at this time.</p>
            <a href="{{ route('inventory.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-boxes me-1"></i> View Full Inventory
            </a>
        </div>
    </div>
    @endif
</div>
@endsection