@extends('layouts.app')

@section('title', 'Quick Sale - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quick Sale</h1>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Sales
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Barcode Scanner</h5>
                </div>
                <div class="card-body text-center">
                    <div class="barcode-scanner mb-4 p-4 border rounded bg-light">
                        <div class="scanner-container mb-3">
                            <input type="text" class="form-control form-control-lg text-center" 
                                id="barcode-input" placeholder="Scan barcode or enter manually" autofocus>
                            <small class="text-muted">Press Enter after scanning</small>
                        </div>
                        <div class="scanner-status alert alert-info d-none" id="scanner-status">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="status-message">Ready to scan</span>
                        </div>
                    </div>

                    <div class="scanned-products">
                        <h6 class="mb-3">Scanned Products</h6>
                        <div class="table-responsive">
                            <table class="table table-striped" id="scanned-products-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="scanned-products-body">
                                    <!-- Products will be added here dynamically -->
                                </tbody>
                                <tfoot id="scanned-products-footer" class="d-none">
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Subtotal:</strong></td>
                                        <td colspan="3"><strong id="cart-subtotal">{{ $currencySymbol }}0.00</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Sale Summary</h5>
                </div>
                <div class="card-body">
                    <form id="quick-sale-form">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="cart-summary mb-3 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="summary-subtotal">{{ $currencySymbol }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (0%):</span>
                                <span id="summary-tax">{{ $currencySymbol }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount:</span>
                                <span id="summary-discount">{{ $currencySymbol }}0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2 fw-bold">
                                <span>Total:</span>
                                <span id="summary-total">{{ $currencySymbol }}0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg" id="complete-sale-btn" disabled>
                            <i class="fas fa-check-circle me-2"></i> Complete Sale
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Invoice Modal -->
<div class="modal fade" id="printInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sale Completed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                    <h4>Sale Completed Successfully!</h4>
                    <p class="mb-1">Sale #: <strong id="sale-number"></strong></p>
                    <p>Total Amount: <strong id="sale-total"></strong></p>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i> View All Sales
                    </a>
                    <a href="#" class="btn btn-primary" id="print-invoice-btn">
                        <i class="fas fa-print me-1"></i> Print Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .barcode-scanner {
        position: relative;
    }
    
    .scanner-laser {
        position: absolute;
        margin: 0 auto;
        height: 2px;
        width: 100%;
        background-color: red;
        opacity: 0.5;
        top: 50%;
        z-index: 1;
        animation: scanning 2s infinite;
    }
    
    @keyframes scanning {
        0% { transform: translateY(-50px); }
        50% { transform: translateY(50px); }
        100% { transform: translateY(-50px); }
    }
    
    .scanned-product-row {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let cart = [];
        const barcodeInput = document.getElementById('barcode-input');
        const scannedProductsBody = document.getElementById('scanned-products-body');
        const scannedProductsFooter = document.getElementById('scanned-products-footer');
        const cartSubtotal = document.getElementById('cart-subtotal');
        const summarySubtotal = document.getElementById('summary-subtotal');
        const summaryTotal = document.getElementById('summary-total');
        const completeSaleBtn = document.getElementById('complete-sale-btn');
        const scannerStatus = document.getElementById('scanner-status');
        const statusMessage = document.getElementById('status-message');
        const quickSaleForm = document.getElementById('quick-sale-form');
        const printInvoiceModal = new bootstrap.Modal(document.getElementById('printInvoiceModal'));
        const printInvoiceBtn = document.getElementById('print-invoice-btn');
        const saleNumberElement = document.getElementById('sale-number');
        const saleTotalElement = document.getElementById('sale-total');

        // Barcode scanning functionality
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = this.value.trim();
                
                if (barcode) {
                    scanBarcode(barcode);
                    this.value = '';
                }
            }
        });

        function scanBarcode(barcode) {
            showScannerStatus('Scanning product...', 'info');
            
            fetch('{{ route("sales.scan-barcode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: barcode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addProductToCart(data.product);
                    showScannerStatus('Product added successfully!', 'success');
                } else {
                    showScannerStatus(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showScannerStatus('Error scanning barcode', 'danger');
            });
        }

        function addProductToCart(product) {
            // Check if product already exists in cart
            const existingItemIndex = cart.findIndex(item => item.id === product.id);
            
            if (existingItemIndex >= 0) {
                // Increment quantity if product already exists
                cart[existingItemIndex].quantity += 1;
                cart[existingItemIndex].total = cart[existingItemIndex].quantity * cart[existingItemIndex].price;
                
                // Update the row in the table
                const quantityCell = document.querySelector(`tr[data-product-id="${product.id}"] .product-quantity`);
                const totalCell = document.querySelector(`tr[data-product-id="${product.id}"] .product-total`);
                
                quantityCell.textContent = cart[existingItemIndex].quantity;
                totalCell.textContent = '{{ $currencySymbol }}' + cart[existingItemIndex].total.toFixed(2);
            } else {
                // Add new product to cart
                const cartItem = {
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    total: product.price,
                    barcode: product.barcode
                };
                
                cart.push(cartItem);
                
                // Add new row to the table
                const newRow = document.createElement('tr');
                newRow.className = 'scanned-product-row';
                newRow.setAttribute('data-product-id', product.id);
                newRow.innerHTML = `
                    <td>${product.name}</td>
                    <td>{{ $currencySymbol }}${product.price.toFixed(2)}</td>
                    <td class="product-quantity">1</td>
                    <td class="product-total">{{ $currencySymbol }}${product.price.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product" data-product-id="${product.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                scannedProductsBody.appendChild(newRow);
                
                // Add event listener to remove button
                newRow.querySelector('.remove-product').addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    removeProductFromCart(productId);
                });
            }
            
            // Show footer if it was hidden
            scannedProductsFooter.classList.remove('d-none');
            
            // Update cart totals
            updateCartTotals();
        }

        function removeProductFromCart(productId) {
            // Remove product from cart array
            cart = cart.filter(item => item.id != productId);
            
            // Remove row from table
            const rowToRemove = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (rowToRemove) {
                rowToRemove.remove();
            }
            
            // Hide footer if cart is empty
            if (cart.length === 0) {
                scannedProductsFooter.classList.add('d-none');
            }
            
            // Update cart totals
            updateCartTotals();
        }

        function updateCartTotals() {
            const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
            const tax = 0; // You can implement tax calculation if needed
            const discount = 0; // You can implement discount calculation if needed
            const total = subtotal + tax - discount;
            
            cartSubtotal.textContent = '{{ $currencySymbol }}' + subtotal.toFixed(2);
            summarySubtotal.textContent = '{{ $currencySymbol }}' + subtotal.toFixed(2);
            summaryTotal.textContent = '{{ $currencySymbol }}' + total.toFixed(2);
            
            // Enable or disable complete sale button
            completeSaleBtn.disabled = cart.length === 0;
        }

        function showScannerStatus(message, type) {
            scannerStatus.classList.remove('d-none', 'alert-info', 'alert-success', 'alert-danger');
            scannerStatus.classList.add(`alert-${type}`);
            statusMessage.textContent = message;
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                scannerStatus.classList.add('d-none');
            }, 3000);
        }

        // Form submission
        quickSaleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (cart.length === 0) {
                showScannerStatus('Please scan at least one product', 'danger');
                return;
            }
            
            // Prepare items for submission
            const items = cart.map(item => {
                return {
                    product_id: item.id,
                    quantity: item.quantity
                };
            });
            
            const formData = {
                items: items,
                payment_method: document.getElementById('payment_method').value
            };
            
            // Disable button during processing
            completeSaleBtn.disabled = true;
            completeSaleBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            
            // Submit the sale
            fetch('{{ route("sales.quick-process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success modal
                    saleNumberElement.textContent = data.sale_number;
                    saleTotalElement.textContent = '{{ $currencySymbol }}' + summaryTotal.textContent.replace('{{ $currencySymbol }}', '');
                    
                    // Set print button URL
                    printInvoiceBtn.href = `/sales/${data.sale_id}/receipt`;
                    
                    // Show modal
                    printInvoiceModal.show();
                    
                    // Reset form
                    cart = [];
                    scannedProductsBody.innerHTML = '';
                    scannedProductsFooter.classList.add('d-none');
                    updateCartTotals();
                } else {
                    showScannerStatus(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showScannerStatus('Error processing sale', 'danger');
            })
            .finally(() => {
                // Re-enable button
                completeSaleBtn.disabled = cart.length === 0;
                completeSaleBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Complete Sale';
            });
        });

        // Focus on barcode input when modal is closed
        document.getElementById('printInvoiceModal').addEventListener('hidden.bs.modal', function () {
            barcodeInput.focus();
        });
    });
</script>
@endsection