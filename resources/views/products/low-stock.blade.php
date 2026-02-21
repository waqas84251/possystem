@extends('layouts.app')

@section('title', 'Low Stock Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Low Stock Products
                        <span class="badge bg-danger ms-2">{{ $lowStockCount }}</span>
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Category</th>
                                    <th scope="col" class="text-center">Current Stock</th>
                                    <th scope="col" class="text-center">Threshold</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-end">Price</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                <tr class="@if($product->stock == 0) table-danger @else table-warning @endif">
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail me-2" width="40" height="40">
                                            @else
                                                <div class="bg-secondary d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-image text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                    <td class="text-center fw-bold @if($product->stock == 0) text-danger @else text-warning @endif">
                                        {{ $product->stock }}
                                    </td>
                                    <td class="text-center">{{ $product->low_stock_threshold }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.print-barcode', $product->id) }}" class="btn btn-secondary" title="Print Barcode" target="_blank">
                                                <i class="fas fa-barcode"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h4>No Low Stock Products</h4>
                        <p class="text-muted">All products have sufficient stock levels.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript functionality needed for the low stock page
        console.log('Low stock products page loaded');
    });
</script>
@endsection

@section('styles')
<style>
    @media print {
        .card-header .btn-group, .card-header .btn {
            display: none !important;
        }
        
        .table-danger, .table-warning {
            background-color: transparent !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .img-thumbnail {
        object-fit: cover;
    }
</style>
@endsection