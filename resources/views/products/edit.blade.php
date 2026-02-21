@extends('layouts.app')

@section('title', 'Edit Product - ' . $product->name)

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Edit Product</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Edit Product</li>
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
                        <h4 class="card-title">Edit Product: {{ $product->name }}</h4>
                        <div>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-info">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Products
                            </a>
                        </div>
                    </div>

                    <form id="editProductForm" method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden field to track old stock -->
                        <input type="hidden" name="old_stock" value="{{ $product->stock }}">
                        
                        <div class="row">
                            <!-- Left Column - Product Information -->
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Product Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                        id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                                        id="category_id" name="category_id" required>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" 
                                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ $currencySymbol }}</span>
                                                        <input type="number" step="0.01" min="0.01" 
                                                            class="form-control @error('price') is-invalid @enderror" 
                                                            id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                                    </div>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cost_price" class="form-label">Cost Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ $currencySymbol }}</span>
                                                        <input type="number" step="0.01" min="0" 
                                                            class="form-control @error('cost_price') is-invalid @enderror" 
                                                            id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                                                    </div>
                                                    @error('cost_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Must be less than selling price</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('status') is-invalid @enderror" 
                                                        id="status" name="status" required>
                                                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                                                    <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" 
                                                        id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                                    @error('stock')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                                    <input type="number" min="0" class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                                        id="low_stock_threshold" name="low_stock_threshold" 
                                                        value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}">
                                                    @error('low_stock_threshold')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Alert when stock reaches this level</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="sku" class="form-label">SKU</label>
                                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                        id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                                                    @error('sku')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Image and Barcode -->
                            <div class="col-md-4">
                                <!-- Image Upload Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Product Image</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="img-fluid rounded" 
                                                    style="max-height: 200px;"
                                                    id="productImagePreview">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                    style="height: 200px;">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Upload New Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                id="image" name="image" accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Max file size: 5MB. Formats: jpeg, png, jpg, gif, webp</small>
                                        </div>
                                        
                                        @if($product->image)
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                Remove current image
                                            </label>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Barcode Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Barcode</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="barcode" class="form-label">Barcode Number</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                                    id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                                                <button type="button" class="btn btn-outline-secondary" id="generate-barcode">
                                                    <i class="fas fa-barcode"></i> Generate
                                                </button>
                                            </div>
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div id="barcode-preview" class="text-center mt-3">
                                            @if($product->barcode)
                                                <div class="alert alert-info p-2">
                                                    <small>Current Barcode:</small><br>
                                                    <div class="text-center" id="current-barcode">
                                                        {!! $barcodeSvg !!}
                                                    </div>
                                                    <small class="d-block text-center mt-1">{{ $product->barcode }}</small>
                                                </div>
                                            @endif
                                        </div>

                                        @if($product->barcode)
                                        <div class="d-grid gap-2 mt-3">
                                            <a href="{{ route('products.barcode.print', $product->id) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-print me-1"></i> Print Barcode
                                            </a>
                                            <a href="{{ route('products.download-barcode', $product->barcode) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-download me-1"></i> Download Barcode
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary" id="submitButton">
                                            <i class="fas fa-save me-1"></i> Update Product
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="cancelButton">
                                            Cancel
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                                        <i class="fas fa-trash-alt me-1"></i> Delete Product
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                <p class="text-danger">This action cannot be undone and will permanently remove the product from the system.</p>
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

<!-- Debug Information (temporary) -->
<div class="alert alert-info d-none mt-3" id="debugInfo">
    <h6>Debug Information:</h6>
    <div>Current Stock: <span id="debugCurrentStock">{{ $product->stock }}</span></div>
    <div>Form Stock Value: <span id="debugFormStock"></span></div>
</div>
@endsection

@push('styles')
<style>
    .image-preview-container {
        border: 2px dashed #dee2e6;
        border-radius: 5px;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1rem;
    }
    .image-preview {
        max-width: 100%;
        max-height: 200px;
    }
    #current-barcode svg {
        max-width: 100%;
        height: 60px;
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
        
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('productImagePreview');
        
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Barcode generation
        const generateBarcodeBtn = document.getElementById('generate-barcode');
        const barcodeInput = document.getElementById('barcode');
        const barcodePreview = document.getElementById('barcode-preview');
        
        if (generateBarcodeBtn && barcodeInput) {
            generateBarcodeBtn.addEventListener('click', function() {
                fetch('{{ route("products.generate-barcode") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        barcodeInput.value = data.barcode;
                        if (barcodePreview) {
                            barcodePreview.innerHTML = 
                                '<div class="alert alert-info p-2">' +
                                '<small>New Barcode Preview:</small><br>' +
                                '<div class="text-center">' + data.image + '</div>' +
                                '<small class="d-block text-center mt-1">' + data.barcode + '</small>' +
                                '</div>';
                        }
                    } else {
                        showToast('Error', 'Error generating barcode: ' + (data.error || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error', 'Error generating barcode', 'error');
                });
            });
        }
        
        // Cost price validation
        const costPriceInput = document.getElementById('cost_price');
        const priceInput = document.getElementById('price');
        
        if (costPriceInput && priceInput) {
            costPriceInput.addEventListener('change', function() {
                const costPrice = parseFloat(this.value);
                const price = parseFloat(priceInput.value);
                
                if (costPrice && price && costPrice >= price) {
                    showToast('Validation Error', 'Cost price must be less than selling price.', 'error');
                    this.value = '';
                    this.focus();
                }
            });
        }
        
        // Debug: Show current vs form values
        const stockInput = document.getElementById('stock');
        if (stockInput) {
            stockInput.addEventListener('change', function() {
                document.getElementById('debugFormStock').textContent = this.value;
                document.getElementById('debugInfo').classList.remove('d-none');
            });
        }
        
        // Form submission
        const editProductForm = document.getElementById('editProductForm');
        const submitButton = document.getElementById('submitButton');
        
        if (editProductForm) {
            editProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Disable submit button to prevent double submission
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
                
                // Convert form to FormData for file upload
                const formData = new FormData(this);
                
                // Add debug info
                console.log('Form data being sent:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        // Remove Content-Type header when using FormData
                        // The browser will set it automatically with the correct boundary
                    },
                    body: formData
                })
                .then(response => {
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // Handle non-JSON response (redirects, etc.)
                        return response.text().then(text => {
                            throw new Error('Expected JSON response, got: ' + text);
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showToast('Success', data.message, 'success');
                        
                        // Redirect to show page after a delay
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        // Re-enable submit button
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Update Product';
                        
                        // Show validation errors
                        if (data.errors) {
                            let errorMessage = 'Please correct the following errors:\n';
                            for (const field in data.errors) {
                                errorMessage += `- ${data.errors[field][0]}\n`;
                            }
                            showToast('Validation Error', errorMessage, 'error');
                        } else {
                            showToast('Error', data.message || 'An error occurred', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Update Product';
                    showToast('Error', 'An error occurred while updating the product: ' + error.message, 'error');
                });
            });
        }
        
        // Cancel button
        const cancelButton = document.getElementById('cancelButton');
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                window.location.href = '{{ route('products.index') }}';
            });
        }
        
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