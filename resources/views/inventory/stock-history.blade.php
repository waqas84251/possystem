@extends('layouts.app')

@section('title', 'Stock History - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Stock History: {{ $product->name }}</h1>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Product Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Product:</strong> {{ $product->name }}<br>
                    <strong>Category:</strong> {{ $product->category->name }}<br>
                    <strong>Current Stock:</strong> <span class="fw-bold">{{ $product->stock }} units</span>
                </div>
                <div class="col-md-6">
                    <strong>Price:</strong> ${{ number_format($product->price, 2) }}<br>
                    <strong>Stock Value:</strong> ${{ number_format($product->price * $product->stock, 2) }}<br>
                    <strong>Status:</strong> 
                    @if($product->stock == 0)
                        <span class="badge bg-danger">Out of Stock</span>
                    @elseif($product->stock < 5)
                        <span class="badge bg-warning">Low Stock</span>
                    @else
                        <span class="badge bg-success">In Stock</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Stock History</h5>
        </div>
        <div class="card-body">
            <div class="text-center py-4">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Stock History Feature</h4>
                <p class="text-muted">This feature will show complete stock adjustment history when implemented.</p>
                <p class="text-muted">Would require an <code>inventory_logs</code> table to track all stock changes.</p>
                <a href="{{ route('inventory.show-adjust', $product) }}" class="btn btn-primary mt-2">
                    <i class="fas fa-edit me-1"></i> Adjust Stock
                </a>
            </div>
        </div>
    </div>
</div>
@endsection