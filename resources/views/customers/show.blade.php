@extends('layouts.app')

@section('title', $customer->name . ' - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Customer Details</h1>
        <div>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Customers
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong>
                        <p class="mb-0">{{ $customer->name }}</p>
                    </div>
                    
                    @if($customer->email)
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p class="mb-0">{{ $customer->email }}</p>
                    </div>
                    @endif
                    
                    @if($customer->phone)
                    <div class="mb-3">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $customer->phone }}</p>
                    </div>
                    @endif
                    
                    @if($customer->address)
                    <div class="mb-3">
                        <strong>Address:</strong>
                        <p class="mb-0">{{ $customer->address }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Total Purchases:</strong>
                        <p class="mb-0">{{ $customer->sales_count }} orders</p>
                    </div>
                    
                    <div class="mb-0">
                        <strong>Total Spent:</strong>
                        <p class="mb-0">{{ $currencySymbol }}{{ number_format($customer->sales->sum('total_amount'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Purchase History</h5>
                </div>
                <div class="card-body">
                    @if($customer->sales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sale #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->sales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="text-primary">
                                            {{ $sale->sale_number }}
                                        </a>
                                    </td>
                                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
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
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No purchase history found</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection