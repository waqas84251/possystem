@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container-fluid px-4" style="height: 100vh;">
    <div class="row h-100">
        <div class="col-12 h-100">
            <div class="card h-100">
                <div class="card-body p-0 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <h4 class="card-title mb-0">Add New Product</h4>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    <form id="createProductForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="flex-grow-1" style="overflow-y: auto;">
                        @csrf
                        
                        <div class="row p-3">
                            <!-- Left Column - Product Information -->
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name') }}" required
                                                placeholder="Enter product name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

                                <div class="mb-2">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="2" 
                                        placeholder="Brief product description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                                <input type="number" step="0.01" min="0.01" 
                                                    class="form-control @error('price') is-invalid @enderror" 
                                                    id="price" name="price" value="{{ old('price') }}" required
                                                    placeholder="0.00">
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="cost_price" class="form-label">Cost Price</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                                <input type="number" step="0.01" min="0" 
                                                    class="form-control @error('cost_price') is-invalid @enderror" 
                                                    id="cost_price" name="cost_price" value="{{ old('cost_price') }}"
                                                    placeholder="0.00">
                                            </div>
                                            @error('cost_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Must be less than selling price</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                                            <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" 
                                                id="stock" name="stock" value="{{ old('stock', 0) }}" required
                                                placeholder="0">
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="low_stock_threshold" class="form-label">Low Stock Alert</label>
                                            <input type="number" min="0" class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                                id="low_stock_threshold" name="low_stock_threshold" 
                                                value="{{ old('low_stock_threshold', 10) }}"
                                                placeholder="10">
                                            @error('low_stock_threshold')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="sku" class="form-label">SKU</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                    id="sku" name="sku" value="{{ old('sku') }}"
                                                    placeholder="Auto-generated">
                                                <button type="button" class="btn btn-outline-secondary" id="generate-sku" title="Generate SKU">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </div>
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Image and Barcode -->
                            <div class="col-lg-4">
                                <div class="sticky-top" style="top: 20px; z-index: 1;">
                                    <!-- Image Upload -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title mb-0">Product Image</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="text-center mb-2">
                                                <div class="image-preview-container border-dashed rounded" id="imageDropArea" style="min-height: 120px;">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-1"></i>
                                                    <p class="text-muted mb-1 small">Drag & drop or click to upload</p>
                                                    <p class="text-muted small">Max 5MB • JPG, PNG, GIF, WEBP</p>
                                                    <img src="" class="image-preview d-none" id="imagePreview" alt="Image preview" style="max-height: 100px;">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <input type="file" class="form-control @error('image') is-invalid @enderror d-none" 
                                                    id="image" name="image" accept="image/*">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Barcode -->
                                    <div class="card">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title mb-0">Barcode</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="mb-2">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                                        id="barcode" name="barcode" value="{{ old('barcode') }}"
                                                        placeholder="Auto-generated">
                                                    <button type="button" class="btn btn-outline-secondary" id="generate-barcode" title="Generate Barcode">
                                                        <i class="fas fa-barcode"></i>
                                                    </button>
                                                </div>
                                                @error('barcode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div id="barcode-preview" class="text-center mt-2">
                                                <!-- Barcode preview will be shown here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row p-3 bg-light border-top mt-auto">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="add_another" name="add_another">
                                        <label class="form-check-label" for="add_another">
                                            Add another product
                                        </label>
                                    </div>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary btn-sm me-2">
                                            <i class="fas fa-undo me-1"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submitButton">
                                            <i class="fas fa-plus-circle me-1"></i> Create
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-dashed {
        border: 2px dashed #dee2e6 !important;
    }
    .image-preview-container {
        padding: 1rem;
        text-align: center;
        margin-bottom: 0.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #fafafa;
    }
    .image-preview-container:hover {
        border-color: #007bff !important;
        background-color: #f0f8ff;
    }
    .image-preview {
        max-width: 100%;
        object-fit: contain;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.3rem;
        color: #495057;
        font-size: 0.9rem;
    }
    .sticky-top {
        position: sticky;
        top: 10px;
    }
    .input-group-text {
        background-color: #f8f9fa;
        font-size: 0.875rem;
    }
    .form-control, .form-select, .input-group {
        font-size: 0.875rem;
    }
    .btn {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Image upload with drag & drop
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const imageDropArea = document.getElementById('imageDropArea');
        const imagePlaceholder = imageDropArea.querySelector('.fa-cloud-upload-alt').parentElement;
        
        // Click on drop area to trigger file input
        imageDropArea.addEventListener('click', function() {
            imageInput.click();
        });
        
        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageDropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            imageDropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            imageDropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            imageDropArea.style.borderColor = '#007bff';
            imageDropArea.style.backgroundColor = '#f0f8ff';
        }
        
        function unhighlight() {
            imageDropArea.style.borderColor = '#dee2e6';
            imageDropArea.style.backgroundColor = '#fafafa';
        }
        
        imageDropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            imageInput.files = files;
            handleFiles(files);
        }
        
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                        imagePlaceholder.classList.add('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
        
        imageInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
        
        // Auto-generate SKU
        const productNameInput = document.getElementById('name');
        const skuInput = document.getElementById('sku');
        const generateSkuBtn = document.getElementById('generate-sku');
        
        function generateSKU(name) {
            if (!name) return '';
            
            // Convert name to uppercase and remove special characters
            let sku = name.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // Take first 6 characters
            sku = sku.substring(0, 6);
            
            // Add random numbers if needed
            if (sku.length < 6) {
                sku += Math.random().toString().substring(2, 2 + (6 - sku.length));
            }
            
            // Add random numbers to make it unique
            sku += Math.random().toString().substring(2, 5);
            
            return sku;
        }
        
        if (productNameInput && skuInput) {
            productNameInput.addEventListener('blur', function() {
                if (!skuInput.value && this.value) {
                    skuInput.value = generateSKU(this.value);
                }
            });
            
            generateSkuBtn.addEventListener('click', function() {
                skuInput.value = generateSKU(productNameInput.value);
            });
        }
        
        // Barcode generation
        const generateBarcodeBtn = document.getElementById('generate-barcode');
        const barcodeInput = document.getElementById('barcode');
        const barcodePreview = document.getElementById('barcode-preview');
        
        function generateBarcode() {
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
                    
                    // Decode the base64 SVG and render it properly
                    const svgContent = atob(data.image.replace('data:image/svg+xml;base64,', ''));
                    barcodePreview.innerHTML = 
                        '<div class="alert alert-info p-1 mb-0">' +
                        '<small class="d-block text-center mb-1">Barcode Preview</small>' +
                        '<div class="text-center">' + svgContent + '</div>' +
                        '<small class="d-block text-center mt-1">' + data.barcode + '</small>' +
                        '</div>';
                } else {
                    showToast('Error', 'Error generating barcode: ' + (data.error || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Error generating barcode', 'error');
            });
        }
        
        if (generateBarcodeBtn && barcodeInput) {
            generateBarcodeBtn.addEventListener('click', generateBarcode);
            
            // Auto-generate barcode on page load if empty
            if (!barcodeInput.value) {
                generateBarcode();
            }
        }
        
        // Auto-generate SKU on page load if empty and product name exists
        if (!skuInput.value && productNameInput.value) {
            skuInput.value = generateSKU(productNameInput.value);
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
        
        // Toast notification function
        function showToast(title, message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(toast);
            
            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove after hide
            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toast);
            });
        }
    });
</script>
@endpush