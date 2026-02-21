@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Product Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($product->name, 20) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Product: {{ $product->name }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Products
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Column - Product Image & Basic Info -->
                        <div class="col-md-4">
                            <!-- Product Image Card -->
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-fluid rounded" 
                                             style="max-height: 300px;"
                                             id="productImage">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <h3 class="mt-3">{{ $product->name }}</h3>
                                    <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                    
                                    <div class="mt-3 d-flex gap-2 justify-content-center">
                                        <a href="{{ route('products.barcode.print', $product->id) }}" 
                                           target="_blank" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-print me-1"></i> Print Barcode
                                        </a>
                                        @if($product->barcode)
                                        <a href="{{ route('products.download-barcode', $product->barcode) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-download me-1"></i> Download Barcode
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inventory Summary Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Inventory Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span>Current Stock:</span>
                                        <span class="fw-bold fs-5 {{ $product->stock == 0 ? 'text-danger' : ($product->stock <= $product->low_stock_threshold ? 'text-warning' : 'text-success') }}">
                                            {{ $product->stock }}
                                        </span>
                                    </div>
                                    
                                    <div class="progress mb-3" style="height: 10px;">
                                        @php
                                            $maxStock = max($product->stock, $product->low_stock_threshold, 10);
                                            $stockPercentage = ($product->stock / $maxStock) * 100;
                                            $progressClass = $product->stock == 0 ? 'bg-danger' : 
                                                            ($product->stock <= $product->low_stock_threshold ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress-bar {{ $progressClass }}" 
                                             role="progressbar" 
                                             style="width: {{ $stockPercentage }}%;" 
                                             aria-valuenow="{{ $stockPercentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="border rounded p-2 text-center">
                                                <div class="text-muted small">Threshold</div>
                                                <div class="fw-bold">{{ $product->low_stock_threshold }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2 text-center">
                                                <div class="text-muted small">Stock Status</div>
                                                <div class="fw-bold">
                                                    @if($product->stock == 0)
                                                        <span class="text-danger">Out of Stock</span>
                                                    @elseif($product->stock <= $product->low_stock_threshold)
                                                        <span class="text-warning">Low Stock</span>
                                                    @else
                                                        <span class="text-success">In Stock</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Identification Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Product Identification</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">SKU</label>
                                        <div class="fw-bold font-monospace">{{ $product->sku }}</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Barcode</label>
                                        <div class="fw-bold font-monospace">{{ $product->barcode ?? 'N/A' }}</div>
                                        @if($product->barcode)
                                        <div class="mt-2 text-center">
                                            {!! $barcodeSvg !!}
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Category</label>
                                        <div>
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $product->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Product Details -->
                        <div class="col-md-8">
                            <!-- Product Details Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Product Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small mb-1">Product Name</label>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small mb-1">Status</label>
                                                <div>
                                                    <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($product->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Description</label>
                                        <div class="fw-light">
                                            {{ $product->description ?? 'No description provided' }}
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-light mb-3">
                                                <div class="card-body text-center">
                                                    <label class="form-label text-muted small mb-1">Selling Price</label>
                                                    <div class="fw-bold fs-4 text-primary">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light mb-3">
                                                <div class="card-body text-center">
                                                    <label class="form-label text-muted small mb-1">Cost Price</label>
                                                    <div class="fw-bold fs-4 text-secondary">
                                                        {{ $product->cost_price ? $currencySymbol . number_format($product->cost_price, 2) : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light mb-3">
                                                <div class="card-body text-center">
                                                    <label class="form-label text-muted small mb-1">Profit Margin</label>
                                                    <div class="fw-bold fs-4 
                                                        @if($product->cost_price)
                                                            @php
                                                                $margin = $product->price - $product->cost_price;
                                                                $marginPercent = ($margin / $product->price) * 100;
                                                            @endphp
                                                            {{ $marginPercent >= 30 ? 'text-success' : ($marginPercent >= 15 ? 'text-warning' : 'text-danger') }}
                                                        @endif">
                                                        @if($product->cost_price)
                                                            {{ number_format($marginPercent, 1) }}%
                                                        @else
                                                            N/A
                                                        @endif
                                                    </div>
                                                    @if($product->cost_price)
                                                    <small class="text-muted">{{ $currencySymbol }}{{ number_format($margin, 2) }} profit per unit</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small mb-1">Created Date</label>
                                                <div class="fw-bold">
                                                    {{ $product->created_at->format('M d, Y h:i A') }}
                                                    <small class="text-muted">({{ $product->created_at->diffForHumans() }})</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small mb-1">Last Updated</label>
                                                <div class="fw-bold">
                                                    {{ $product->updated_at->format('M d, Y h:i A') }}
                                                    <small class="text-muted">({{ $product->updated_at->diffForHumans() }})</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inventory History Card -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Inventory History</h5>
                                    <span class="badge bg-primary">{{ $product->inventory->count() }} records</span>
                                </div>
                                <div class="card-body">
                                    @if($product->inventory->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Type</th>
                                                    <th>Quantity</th>
                                                    <th>User</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->inventory as $record)
                                                <tr>
                                                    <td>
                                                        <div>{{ $record->created_at->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $record->created_at->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $record->type == 'initial' ? 'info' : ($record->type == 'restock' ? 'success' : 'warning') }}">
                                                            {{ ucfirst($record->type) }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold {{ $record->type == 'restock' ? 'text-success' : 'text-danger' }}">
                                                        {{ $record->type == 'restock' ? '+' : '-' }}{{ $record->quantity }}
                                                    </td>
                                                    <td>
                                                        <small>{{ $record->user->name ?? 'System' }}</small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $record->remarks }}</small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No inventory history available</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Quick Actions Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Edit Product
                                                </a>
                                                <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#quickStockModal">
                                                    <i class="fas fa-boxes me-1"></i> Quick Stock Update
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('products.barcode.print', $product->id) }}" target="_blank" class="btn btn-outline-info">
                                                    <i class="fas fa-barcode me-1"></i> Print Barcode Labels
                                                </a>
                                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete Product
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $product->name }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This action cannot be undone. All inventory history will also be deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteProductForm" action="{{ route('products.destroy', $product->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stock Update Modal -->
<div class="modal fade" id="quickStockModal" tabindex="-1" aria-labelledby="quickStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickStockModalLabel">Quick Stock Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickStockForm">
                    @csrf
                    <div class="mb-3">
                        <label for="stock" class="form-label">New Stock Quantity</label>
                        <input type="number" min="0" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Update Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="restock">Restock (Add stock)</option>
                            <option value="adjustment">Adjustment (Correct stock)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Reason for stock update">Quick stock update</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveQuickStock">Update Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toast-title">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    .progress {
        border-radius: 10px;
    }
    .font-monospace {
        font-family: 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', monospace;
    }
    #productImage {
        transition: transform 0.3s ease;
    }
    #productImage:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap toasts
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Delete form submission
        const deleteProductForm = document.getElementById('deleteProductForm');
        if (deleteProductForm) {
            deleteProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Success', data.message, 'success');
                            setTimeout(() => {
                                window.location.href = '{{ route('products.index') }}';
                            }, 1500);
                        } else {
                            showToast('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Error', 'An error occurred while deleting the product', 'error');
                    });
                }
            });
        }
        
        // Quick stock update functionality
        const quickStockModal = document.getElementById('quickStockModal');
        const saveQuickStockBtn = document.getElementById('saveQuickStock');
        
        if (saveQuickStockBtn) {
            saveQuickStockBtn.addEventListener('click', function() {
                const formData = new FormData();
                formData.append('stock', document.getElementById('stock').value);
                formData.append('type', document.getElementById('type').value);
                formData.append('remarks', document.getElementById('remarks').value);
                formData.append('_token', csrfToken);
                
                fetch('{{ route('products.quick-update-stock', $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Success', data.message, 'success');
                        // Close the modal
                        const modal = bootstrap.Modal.getInstance(quickStockModal);
                        modal.hide();
                        // Reload the page to see updated stock
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error', 'An error occurred while updating stock', 'error');
                });
            });
        }
        
        // Toast notification function
        function showToast(title, message, type = 'info') {
            const toastTitle = document.getElementById('toast-title');
            const toastMessage = document.getElementById('toast-message');
            
            // Set toast content
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            
            // Set toast color based on type
            const toast = document.getElementById('liveToast');
            toast.className = 'toast';
            if (type === 'success') {
                toast.classList.add('text-bg-success');
            } else if (type === 'error') {
                toast.classList.add('text-bg-danger');
            } else if (type === 'warning') {
                toast.classList.add('text-bg-warning');
            } else {
                toast.classList.add('text-bg-info');
            }
            
            // Show the toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
    });
</script>
@endpush