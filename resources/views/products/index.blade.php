@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom mb-3">
                <h4 class="mb-0 fw-bold text-primary">Products Management</h4>
                <nav aria-label="breadcrumb">
                   <ol class="breadcrumb mb-0 p-2 bg-light border rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active text-secondary" aria-current="page">Products</li>
                    </ol>

                </nav>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Product Inventory</h4>
                        <div class="d-flex gap-2">
                            <!-- Low Stock Button - Fixed -->
                            <a href="{{ route('products.low-stock') }}" class="btn btn-warning position-relative">
                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                                @if($lowStockCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $lowStockCount }}+
                                </span>
                                @endif
                            </a>
                            
                            @auth
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Add Product
                            </a>
                            @endauth
                           
                        </div>
                    </div>

                    <!-- Filters and Search -->
                    <!-- Enhanced Filters and Search -->
    <div class="row mb-4">
        <div class="col-md-12">
            <form id="filterForm" method="GET" action="{{ route('products.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-input-group position-relative">
                            <div class="input-group">
                                <select class="search-type-selector form-select" style="max-width: 130px;" id="searchType">
                                    <option value="all">All Fields</option>
                                    <option value="name">Name</option>
                                    <option value="sku">SKU</option>
                                    <option value="barcode">Barcode</option>
                                    <option value="category">Category</option>
                                    <option value="status">Status</option>
                                    <option value="stock">Stock Level</option>
                                </select>
                                <input type="text" name="search" class="form-control" placeholder="Search products..." 
                                       value="{{ request('search') }}" id="searchInput">
                            </div>
                            <div class="search-suggestions" id="searchSuggestions"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="stock_status" class="form-select" id="stockFilter">
                            <option value="">All Stock</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter me-1"></i> Apply
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Active Filters Display -->
                @if(request()->anyFilled(['search', 'status', 'category', 'stock_status']))
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-2 text-muted small">Active filters:</span>
                            @if(request('search'))
                                <span class="badge bg-primary me-2 mb-2 filter-badge">
                                    Search: {{ request('search') }}
                                    <a href="{{ remove_filter_url('search') }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="badge bg-info me-2 mb-2 filter-badge">
                                    Status: {{ ucfirst(request('status')) }}
                                    <a href="{{ remove_filter_url('status') }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('category') && $categories->find(request('category')))
                                <span class="badge bg-success me-2 mb-2 filter-badge">
                                    Category: {{ $categories->find(request('category'))->name }}
                                    <a href="{{ remove_filter_url('category') }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('stock_status'))
                                <span class="badge bg-warning me-2 mb-2 filter-badge">
                                    Stock: {{ request('stock_status') == 'low' ? 'Low Stock' : 'Out of Stock' }}
                                    <a href="{{ remove_filter_url('stock_status') }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0" id="productsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    @auth
                                    <th>Actions</th>
                                    @endauth
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr id="product-row-{{ $product->id }}" class="{{ $product->stock == 0 ? 'table-danger' : ($product->stock <= $product->low_stock_threshold ? 'table-warning' : '') }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="rounded" 
                                                         style="width: 48px; height: 48px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 48px; height: 48px;">
                                                        <i class="fas fa-box text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                <small class="text-muted">{{ $product->barcode ?? 'No barcode' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ $product->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td><code>{{ $product->sku }}</code></td>
                                    <td>
                                        <div class="fw-bold">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</div>
                                        @if($product->cost_price)
                                            <small class="text-muted">Cost: {{ $currencySymbol }}{{ number_format($product->cost_price, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                                @php
                                                    $stockPercentage = $product->low_stock_threshold > 0 
                                                        ? min(100, ($product->stock / $product->low_stock_threshold) * 100) 
                                                        : 0;
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
                                            <div class="ms-2">
                                                <span class="{{ $product->stock == 0 ? 'text-danger fw-bold' : ($product->stock <= $product->low_stock_threshold ? 'text-warning fw-bold' : '') }}">
                                                    {{ $product->stock }}
                                                </span>
                                                @if($product->low_stock_threshold > 0)
                                                <small class="text-muted d-block">/{{ $product->low_stock_threshold }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   data-product-id="{{ $product->id }}"
                                                   {{ $product->status == 'active' ? 'checked' : '' }}
                                                   @guest disabled @endguest>
                                            <label class="form-check-label" for="flexSwitchCheckDefault">
                                                <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    @auth
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product->id) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit Product">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.barcode.print', $product->id) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-info" 
                                               data-bs-toggle="tooltip" 
                                               title="Print Barcode">
                                                <i class="fas fa-barcode"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-product" 
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->name }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Delete Product">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                    @endauth
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open fa-3x text-muted mb-2"></i>
                                            <h5 class="text-muted">No products found</h5>
                                            <p class="text-muted">
                                                @if(request()->anyFilled(['search', 'status', 'category', 'stock_status']))
                                                    Try adjusting your search filters
                                                @else
                                                    Get started by adding your first product
                                                @endif
                                            </p>
                                            @if(!request()->anyFilled(['search', 'status', 'category', 'stock_status']))
                                            <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i> Add Product
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                                </div>
                                <div>
                                    {{ $products->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
                <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                <p class="text-danger">This action cannot be undone and will permanently remove the product from the system.</p>
                <div id="deleteError" class="alert alert-danger d-none mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteProductForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteSubmitButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .btn-group .btn {
        border-radius: 0.25rem;
    }
    .export-processing {
        position: relative;
    }
    .export-processing::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<!-- Include SheetJS for Excel export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<!-- Include jsPDF for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Delete product functionality
        const deleteButtons = document.querySelectorAll('.delete-product');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
        const deleteProductName = document.getElementById('deleteProductName');
        const deleteProductForm = document.getElementById('deleteProductForm');
        const deleteError = document.getElementById('deleteError');
        const deleteSubmitButton = document.getElementById('deleteSubmitButton');
        const toastEl = document.getElementById('liveToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl);

        // Function to show toast notifications
        function showToast(title, message, type = 'info') {
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            
            // Remove previous color classes
            toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning', 'text-bg-info');
            
            // Add appropriate color class
            if (type === 'success') {
                toastEl.classList.add('text-bg-success');
            } else if (type === 'error') {
                toastEl.classList.add('text-bg-danger');
            } else if (type === 'warning') {
                toastEl.classList.add('text-bg-warning');
            } else {
                toastEl.classList.add('text-bg-info');
            }
            
            toast.show();
        }

        // Set up delete button click handlers
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                
                // Update modal content
                deleteProductName.textContent = productName;
                deleteProductForm.action = `/products/${productId}`;
                
                // Reset error message
                deleteError.classList.add('d-none');
                deleteError.textContent = '';
                
                // Show the modal
                deleteModal.show();
            });
        });

        // Handle delete form submission
        deleteProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const spinner = deleteSubmitButton.querySelector('.spinner-border');
            spinner.classList.remove('d-none');
            deleteSubmitButton.disabled = true;
            
            // Get the form data
            const formData = new FormData(this);
            const url = this.action;
            
            // Send AJAX request
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('Success', data.message, 'success');
                    
                    // Hide the modal
                    deleteModal.hide();
                    
                    // Remove the product row from the table
                    const productRow = document.getElementById(`product-row-${data.productId}`);
                    if (productRow) {
                        productRow.remove();
                    }
                    
                    // Check if table is empty and show message
                    const tableBody = document.querySelector('tbody');
                    if (tableBody.children.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-box-open fa-3x text-muted mb-2"></i>
                                        <h5 class="text-muted">No products found</h5>
                                        <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-1"></i> Add Product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                } else {
                    // Show error message
                    deleteError.textContent = data.message;
                    deleteError.classList.remove('d-none');
                    showToast('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                deleteError.textContent = 'An error occurred while deleting the product. Please try again.';
                deleteError.classList.remove('d-none');
                showToast('Error', 'An error occurred while deleting the product', 'error');
            })
            .finally(() => {
                // Reset button state
                spinner.classList.add('d-none');
                deleteSubmitButton.disabled = false;
            });
        });

        // Status toggle functionality
        const statusToggles = document.querySelectorAll('.status-toggle');
        
        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const productId = this.getAttribute('data-product-id');
                const isActive = this.checked;
                
                fetch(`/products/${productId}/toggle-status`, {
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
                        // Show success message
                        showToast('Success', 'Product status updated successfully', 'success');
                        
                        // Update the badge text
                        const badge = this.nextElementSibling.querySelector('.badge');
                        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        badge.className = `badge bg-${data.status === 'active' ? 'success' : 'secondary'}`;
                    } else {
                        // Revert the toggle if failed
                        this.checked = !isActive;
                        showToast('Error', data.message || 'Failed to update status', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isActive;
                    showToast('Error', 'An error occurred while updating status', 'error');
                });
            });
        });

        // Search with debounce
        const searchInput = document.getElementById('searchInput');
        let searchTimeout = null;
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }

        // Export functionality
        const exportExcel = document.getElementById('exportExcel');
        const exportPDF = document.getElementById('exportPDF');
        const exportCSV = document.getElementById('exportCSV');
        
        // Helper function to get table data
        function getTableData() {
            const table = document.getElementById('productsTable');
            const headers = [];
            const data = [];
            
            // Get headers
            table.querySelectorAll('thead th').forEach(header => {
                headers.push(header.textContent.trim());
            });
            
            // Get rows data
            table.querySelectorAll('tbody tr').forEach(row => {
                const rowData = [];
                row.querySelectorAll('td').forEach((cell, index) => {
                    // Skip the actions column (last column)
                    if (index < headers.length - 1) {
                        // Handle different types of content in cells
                        if (cell.querySelector('h6')) {
                            rowData.push(cell.querySelector('h6').textContent.trim());
                        } else if (cell.querySelector('.badge')) {
                            rowData.push(cell.querySelector('.badge').textContent.trim());
                        } else if (cell.querySelector('code')) {
                            rowData.push(cell.querySelector('code').textContent.trim());
                        } else if (cell.querySelector('.fw-bold')) {
                            rowData.push(cell.querySelector('.fw-bold').textContent.trim());
                        } else {
                            rowData.push(cell.textContent.trim());
                        }
                    }
                });
                data.push(rowData);
            });
            
            return { headers, data };
        }
        
        // Excel export
        exportExcel.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('export-processing');
            
            setTimeout(() => {
                const { headers, data } = getTableData();
                
                // Create workbook
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet([headers, ...data]);
                
                // Add worksheet to workbook
                XLSX.utils.book_append_sheet(wb, ws, "Products");
                
                // Generate Excel file and download
                XLSX.writeFile(wb, "products_export.xlsx");
                
                this.classList.remove('export-processing');
                showToast('Success', 'Excel file downloaded successfully', 'success');
            }, 500);
        });
        
        // PDF export
        exportPDF.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('export-processing');
            
            setTimeout(() => {
                const { headers, data } = getTableData();
                
                // Initialize jsPDF
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Add title
                doc.setFontSize(18);
                doc.text('Products Inventory', 14, 15);
                doc.setFontSize(11);
                doc.setTextColor(100);
                doc.text(`Exported on: ${new Date().toLocaleString()}`, 14, 22);
                
                // Add table
                doc.autoTable({
                    head: [headers],
                    body: data,
                    startY: 30,
                    theme: 'grid',
                    styles: { fontSize: 9 },
                    headStyles: { fillColor: [66, 139, 202] }
                });
                
                // Save PDF
                doc.save('products_export.pdf');
                
                this.classList.remove('export-processing');
                showToast('Success', 'PDF file downloaded successfully', 'success');
            }, 500);
        });
        
        // CSV export
        exportCSV.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('export-processing');
            
            setTimeout(() => {
                const { headers, data } = getTableData();
                
                // Convert to CSV
                let csvContent = headers.join(',') + '\n';
                data.forEach(row => {
                    csvContent += row.map(field => `"${field}"`).join(',') + '\n';
                });
                
                // Create download link
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                
                link.setAttribute('href', url);
                link.setAttribute('download', 'products_export.csv');
                link.style.visibility = 'hidden';
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                this.classList.remove('export-processing');
                showToast('Success', 'CSV file downloaded successfully', 'success');
            }, 500);
        });
    });
     document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchType = document.getElementById('searchType');
        const searchSuggestions = document.getElementById('searchSuggestions');
        let searchTimeout = null;

        // Function to fetch search suggestions
        function fetchSuggestions(query, type) {
            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }

            fetch(`/products/search-suggestions?query=${encodeURIComponent(query)}&type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let suggestionsHTML = '';
                        data.forEach(item => {
                            suggestionsHTML += `<div class="search-suggestion-item" data-value="${item.value}">${item.label}</div>`;
                        });
                        searchSuggestions.innerHTML = suggestionsHTML;
                        searchSuggestions.style.display = 'block';
                    } else {
                        searchSuggestions.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    searchSuggestions.style.display = 'none';
                });
        }

        // Event listener for search input
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            const type = searchType.value;
            
            if (query.length > 1) {
                searchTimeout = setTimeout(() => {
                    fetchSuggestions(query, type);
                }, 300);
            } else {
                searchSuggestions.style.display = 'none';
            }
        });

        // Event listener for suggestion clicks
        searchSuggestions.addEventListener('click', function(e) {
            if (e.target.classList.contains('search-suggestion-item')) {
                searchInput.value = e.target.getAttribute('data-value');
                searchSuggestions.style.display = 'none';
                document.getElementById('filterForm').submit();
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });

        // Update placeholder based on search type
        searchType.addEventListener('change', function() {
            const placeholders = {
                'all': 'Search in all fields...',
                'name': 'Search by product name...',
                'sku': 'Search by SKU...',
                'barcode': 'Search by barcode...',
                'category': 'Search by category name...',
                'status': 'Search by status (active/inactive)...',
                'stock': 'Search by stock level...'
            };
            searchInput.placeholder = placeholders[this.value] || 'Search products...';
            searchSuggestions.style.display = 'none';
        });

        // Auto-submit filters when changed
        document.getElementById('statusFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        document.getElementById('categoryFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        document.getElementById('stockFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush