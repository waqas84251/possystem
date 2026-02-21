@extends('layouts.app')

@section('title', 'Sale Details - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sale Details</h1>
        <div>
            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-secondary me-2">
                <i class="fas fa-receipt me-1"></i> View Receipt
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Sales
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Sale Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Sale Number:</strong> {{ $sale->sale_number }}
                        </div>
                        <div class="col-md-6">
                            <strong>Date:</strong> {{ $sale->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer:</strong> {{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Cashier:</strong> {{ optional($sale->user)->name ?? 'System' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Payment Method:</strong> 
                            <span class="badge bg-info text-capitalize">{{ $sale->payment_method }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> 
                            <span class="badge bg-success">Completed</span>
                        </div>
                    </div>
                    @if($sale->notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Notes:</strong> {{ $sale->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Sale Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>
                    @if($item->product)
                        {{ $item->product->name }}
                    @else
                        {{ $item->name }} 
                    @endif
                </td>
                <td>{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $currencySymbol }}{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                <td><strong>{{ $currencySymbol }}{{ number_format($sale->subtotal, 2) }}</strong></td>
            </tr>
            @if($sale->discount_amount > 0)
            <tr>
                <td colspan="3" class="text-end"><strong>Discount:</strong></td>
                <td><strong>-{{ $currencySymbol }}{{ number_format($sale->discount_amount, 2) }}</strong></td>
            </tr>
            @endif
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td><strong>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-primary">
                            <i class="fas fa-print me-2"></i> Print Receipt
                        </a>
                        <a href="{{ route('sales.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i> New Sale
                        </a>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure? This will restore product stock.')">
                                <i class="fas fa-trash me-2"></i> Delete Sale
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection