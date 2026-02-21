@extends('layouts.app')

@section('title', 'Sales History - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sales History</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Sale
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="sales-table">
                    <thead>
                        <tr>
                            <th>Sale #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->sale_number }}</td>
                            <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</td>
                            <td>{{ $sale->items->sum('quantity') }} items</td>
                            <td>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-info text-capitalize">{{ $sale->payment_method }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View Sale">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Print Receipt">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Sale" onclick="return confirm('Are you sure? This will restore product stock.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No sales found. <a href="{{ route('sales.create') }}">Create your first sale</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($sales->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} entries
                </div>
                <nav>
                    {{ $sales->links() }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#sales-table').DataTable({
            "order": [[1, "desc"]],
            "responsive": true,
            "pageLength": 10
        });
    });
</script>
@endsection