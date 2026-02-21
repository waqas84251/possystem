@extends('layouts.app')

@section('title', 'Adjust Stock - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Adjust Stock: {{ $product->name }}</h1>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Current Stock Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>Product:</strong><br>
                            <strong>Category:</strong><br>
                            <strong>Current Stock:</strong><br>
                            <strong>Price:</strong><br>
                            <strong>Stock Value:</strong>
                        </div>
                        <div class="col-6">
                            {{ $product->name }}<br>
                            {{ $product->category->name }}<br>
                            <span class="fw-bold text-primary">{{ $product->stock }} units</span><br>
                            ${{ number_format($product->price, 2) }}<br>
                            ${{ number_format($product->price * $product->stock, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Stock Adjustment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.adjust', $product) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="adjustment_type" class="form-label">Adjustment Type *</label>
                            <select class="form-select" id="adjustment_type" name="adjustment_type" required>
                                <option value="add">Add Stock</option>
                                <option value="remove">Remove Stock</option>
                                <option value="set">Set Stock Level</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason *</label>
                            <select class="form-select" id="reason" name="reason" required>
                                <option value="">Select Reason</option>
                                <option value="restock">Restock/New Delivery</option>
                                <option value="damaged">Damaged Goods</option>
                                <option value="return">Customer Return</option>
                                <option value="adjustment">Stock Adjustment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adjustmentType = document.getElementById('adjustment_type');
        const quantityInput = document.getElementById('quantity');
        
        adjustmentType.addEventListener('change', function() {
            if (this.value === 'remove') {
                quantityInput.max = {{ $product->stock }};
                quantityInput.placeholder = 'Max: {{ $product->stock }}';
            } else {
                quantityInput.removeAttribute('max');
                quantityInput.placeholder = '';
            }
        });
    });
</script>
@endsection
@endsection