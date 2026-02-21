@extends('layouts.app')

@section('title', 'Customers - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Customers Management</h1>
        @auth
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Customer
        </a>
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Purchases</th>
                            <th>Total Spent</th>
                            @auth
                            <th>Actions</th>
                            @endauth
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email ?? 'No email' }}</td>
                            <td>{{ $customer->phone ?? 'No phone' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->sales_count }}</span>
                            </td>
                            <td>
                                @php
                                    $totalSpent = $customer->sales->sum('total_amount');
                                @endphp
                                {{ $currencySymbol }}{{ number_format($totalSpent, 2) }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @auth
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Customer">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Customer" onclick="return confirm('Are you sure? This cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No customers found. <a href="{{ route('customers.create') }}">Add your first customer</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection