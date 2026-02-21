@extends('layouts.app')

@section('title', 'New Sale - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">New Sale</h1>
        <div>
            <a href="{{ route('sales.scan') }}" class="btn btn-success me-2">
                <i class="fas fa-camera me-1"></i> Use Camera Scanner
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Sales
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Add Products</h5>
                    <div>
                        <button class="btn btn-sm btn-light me-2 active" id="barcode-tab-btn" data-mode="barcode">
                            <i class="fas fa-barcode me-1"></i> Barcode
                        </button>
                        <button class="btn btn-sm btn-outline-light" id="manual-tab-btn" data-mode="manual">
                            <i class="fas fa-keyboard me-1"></i> Manual Entry
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Barcode Scanner Section -->
                    <div class="barcode-section mb-4 p-3 bg-light rounded">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control" id="barcode-input" 
                                   placeholder="Scan barcode or enter manually" autofocus>
                            <button class="btn btn-success" type="button" id="scan-button">
                                <i class="fas fa-camera me-1"></i> Scan
                            </button>
                        </div>
                        <small class="text-muted">Press Enter or click Scan after entering barcode</small>
                    </div>

                    <!-- Manual Entry Section (Initially Hidden) -->
                    <div class="manual-entry-section mb-4 p-3 bg-light rounded d-none">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label for="product-name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product-name" placeholder="Enter product name">
                            </div>
                            <div class="col-md-3">
                                <label for="product-price" class="form-label">Price ({{ $currencySymbol }})</label>
                                <input type="number" class="form-control" id="product-price" placeholder="0.00" step="0.01" min="0">
                            </div>
                            <div class="col-md-2">
                                <label for="product-quantity" class="form-label">Qty</label>
                                <input type="number" class="form-control" id="product-quantity" value="1" min="1">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" type="button" id="add-manual-product">
                                    <i class="fas fa-plus me-1"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="scanned-products">
                        <h5 class="mb-3">Cart Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="scanned-products-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="scanned-products-body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No products added yet</td>
                                    </tr>
                                </tbody>
                                <tfoot id="scanned-products-footer" class="d-none">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                        <td colspan="2" class="fw-bold" id="cart-subtotal">{{ $currencySymbol }}0.00</td>
                                    </tr>
                                    <tr class="d-none" id="discount-row">
                                        <td colspan="3" class="text-end fw-bold">Discount:</td>
                                        <td colspan="2" class="fw-bold" id="cart-discount">-{{ $currencySymbol }}0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td colspan="2" class="fw-bold" id="cart-total">{{ $currencySymbol }}0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Sale Summary</h5>
                </div>
                <div class="card-body">
                    <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        
                        <!-- Add hidden field for user_id only if user is logged in -->
                        @if(auth()->check())
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        @endif
                        
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer (Optional)</label>
                            <select class="form-select" id="customer_id" name="customer_id">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount ({{ $currencySymbol }})</label>
                            <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                   value="0" step="0.01" min="0" placeholder="0.00">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Add any special instructions or notes here..."></textarea>
                        </div>

                        <div class="cart-summary border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="summary-subtotal">{{ $currencySymbol }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount:</span>
                                <span id="summary-discount">{{ $currencySymbol }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 fw-bold fs-5">
                                <span>Total:</span>
                                <span id="summary-total">{{ $currencySymbol }}0.00</span>
                            </div>
                            <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                            <input type="hidden" name="discount_amount" id="discount-amount-input" value="0">
                            <input type="hidden" name="total_amount" id="total-amount-input" value="0">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg py-2" id="complete-sale-btn" disabled>
                                <i class="fas fa-check-circle me-2"></i> Complete Sale
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="clear-cart-btn">
                                <i class="fas fa-trash me-2"></i> Clear Cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
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
                    <p class="mb-1">Total Amount: <strong id="modal-total-amount">{{ $currencySymbol }}0.00</strong></p>
                    @if(auth()->check())
                        <small class="text-muted">Processed by: {{ auth()->user()->name }}</small>
                    @endif
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-shopping-cart me-1"></i> New Sale
                    </button>
                    <a href="#" class="btn btn-primary" id="print-receipt-btn">
                        <i class="fas fa-print me-1"></i> Print Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .barcode-scanner, .manual-entry-section {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
    }
    
    .scanned-product-row {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .sticky-top {
        z-index: 1020;
    }
    
    #barcode-input:focus, #product-name:focus, #product-price:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    .mode-tab.active {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
</style>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let cart = [];
        let manualProductId = 9000000; // Starting ID for manual products
        const barcodeInput = document.getElementById('barcode-input');
        const scanButton = document.getElementById('scan-button');
        const scannedProductsBody = document.getElementById('scanned-products-body');
        const scannedProductsFooter = document.getElementById('scanned-products-footer');
        const cartSubtotal = document.getElementById('cart-subtotal');
        const cartDiscount = document.getElementById('cart-discount');
        const cartTotal = document.getElementById('cart-total');
        const discountRow = document.getElementById('discount-row');
        const summarySubtotal = document.getElementById('summary-subtotal');
        const summaryDiscount = document.getElementById('summary-discount');
        const summaryTotal = document.getElementById('summary-total');
        const subtotalInput = document.getElementById('subtotal-input');
        const discountInput = document.getElementById('discount_amount');
        const discountAmountInput = document.getElementById('discount-amount-input');
        const totalAmountInput = document.getElementById('total-amount-input');
        const completeSaleBtn = document.getElementById('complete-sale-btn');
        const clearCartBtn = document.getElementById('clear-cart-btn');
        const saleForm = document.getElementById('sale-form');
        const printModal = new bootstrap.Modal(document.getElementById('printModal'));
        const barcodeTabBtn = document.getElementById('barcode-tab-btn');
        const manualTabBtn = document.getElementById('manual-tab-btn');
        const barcodeSection = document.querySelector('.barcode-section');
        const manualSection = document.querySelector('.manual-entry-section');
        const productNameInput = document.getElementById('product-name');
        const productPriceInput = document.getElementById('product-price');
        const productQuantityInput = document.getElementById('product-quantity');
        const addManualProductBtn = document.getElementById('add-manual-product');

        // Tab switching functionality
        barcodeTabBtn.addEventListener('click', function() {
            switchMode('barcode');
        });

        manualTabBtn.addEventListener('click', function() {
            switchMode('manual');
        });

        function switchMode(mode) {
            if (mode === 'barcode') {
                barcodeTabBtn.classList.add('active');
                barcodeTabBtn.classList.remove('btn-outline-light');
                barcodeTabBtn.classList.add('btn-light');
                manualTabBtn.classList.remove('active');
                manualTabBtn.classList.add('btn-outline-light');
                manualTabBtn.classList.remove('btn-light');
                barcodeSection.classList.remove('d-none');
                manualSection.classList.add('d-none');
                barcodeInput.focus();
            } else {
                manualTabBtn.classList.add('active');
                manualTabBtn.classList.remove('btn-outline-light');
                manualTabBtn.classList.add('btn-light');
                barcodeTabBtn.classList.remove('active');
                barcodeTabBtn.classList.add('btn-outline-light');
                barcodeTabBtn.classList.remove('btn-light');
                manualSection.classList.remove('d-none');
                barcodeSection.classList.add('d-none');
                productNameInput.focus();
            }
        }

        // Barcode scanning functionality
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                scanBarcode();
            }
        });

        scanButton.addEventListener('click', scanBarcode);

        function scanBarcode() {
            const barcode = barcodeInput.value.trim();
            
            if (!barcode) {
                showAlert('Please enter a barcode', 'warning');
                return;
            }
            
            // Show loading state
            barcodeInput.disabled = true;
            scanButton.disabled = true;
            scanButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Scanning...';
            
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
                    // Convert price to number to avoid toFixed() error
                    const product = {
                        ...data.product,
                        price: parseFloat(data.product.price)
                    };
                    addProductToCart(product);
                    showAlert(`Added: ${data.product.name}`, 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error scanning barcode', 'danger');
            })
            .finally(() => {
                barcodeInput.disabled = false;
                scanButton.disabled = false;
                scanButton.innerHTML = '<i class="fas fa-camera me-1"></i> Scan';
                barcodeInput.value = '';
                barcodeInput.focus();
            });
        }

        // Manual product entry functionality
        addManualProductBtn.addEventListener('click', addManualProduct);
        
        productNameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addManualProduct();
            }
        });
        
        productPriceInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addManualProduct();
            }
        });

        function addManualProduct() {
            const name = productNameInput.value.trim();
            const price = parseFloat(productPriceInput.value);
            const quantity = parseInt(productQuantityInput.value) || 1;
            
            if (!name) {
                showAlert('Please enter a product name', 'warning');
                productNameInput.focus();
                return;
            }
            
            if (isNaN(price) || price <= 0) {
                showAlert('Please enter a valid price', 'warning');
                productPriceInput.focus();
                return;
            }
            
            if (isNaN(quantity) || quantity < 1) {
                showAlert('Please enter a valid quantity', 'warning');
                productQuantityInput.focus();
                return;
            }
            
            // Create a manual product object
            const manualProduct = {
                id: 'manual_' + manualProductId++, // Use a unique ID for manual products
                name: name,
                price: price, // Already a number from parseFloat
                quantity: quantity,
                total: price * quantity,
                isManual: true // Flag to identify manual products
            };
            
            addProductToCart(manualProduct);
            showAlert(`Added: ${name}`, 'success');
            
            // Reset form
            productNameInput.value = '';
            productPriceInput.value = '';
            productQuantityInput.value = '1';
            productNameInput.focus();
        }

        function addProductToCart(product) {
            // Ensure price is a number
            const price = typeof product.price === 'string' ? parseFloat(product.price) : product.price;
            
            // Check if product already in cart
            const existingItemIndex = cart.findIndex(item => item.id === product.id);
            
            if (existingItemIndex >= 0) {
                // Increment quantity if product already exists
                cart[existingItemIndex].quantity += product.quantity || 1;
                cart[existingItemIndex].total = cart[existingItemIndex].quantity * price;
            } else {
                // Add new product to cart
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: price, // Use the converted price
                    quantity: product.quantity || 1,
                    total: (price * (product.quantity || 1)),
                    isManual: product.isManual || false
                });
            }
            
            updateCartDisplay();
        }

        function updateCartDisplay() {
            if (cart.length === 0) {
                scannedProductsBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No products added yet</td></tr>';
                scannedProductsFooter.classList.add('d-none');
                completeSaleBtn.disabled = true;
            } else {
                let html = '';
                let subtotal = 0;
                
                cart.forEach((item, index) => {
                    // Ensure price and total are numbers
                    const price = typeof item.price === 'string' ? parseFloat(item.price) : item.price;
                    const total = typeof item.total === 'string' ? parseFloat(item.total) : item.total;
                    
                    subtotal += total;
                    html += `
                        <tr class="scanned-product-row">
                            <td>${item.name} ${item.isManual ? '<span class="badge bg-info">Manual</span>' : ''}</td>
                            <td>{{ $currencySymbol }}${price.toFixed(2)}</td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 100px;">
                                    <button class="btn btn-outline-secondary decrease-qty" type="button" data-index="${index}">-</button>
                                    <input type="number" class="form-control text-center quantity-input" value="${item.quantity}" min="1" data-index="${index}">
                                    <button class="btn btn-outline-secondary increase-qty" type="button" data-index="${index}">+</button>
                                </div>
                            </td>
                            <td>{{ $currencySymbol }}${total.toFixed(2)}</td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-item" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                scannedProductsBody.innerHTML = html;
                scannedProductsFooter.classList.remove('d-none');
                
                // Calculate discount and totals
                const discount = parseFloat(discountInput.value) || 0;
                const total = Math.max(0, subtotal - discount);
                
                // Update cart display
                cartSubtotal.textContent = '{{ $currencySymbol }}' + subtotal.toFixed(2);
                cartTotal.textContent = '{{ $currencySymbol }}' + total.toFixed(2);
                
                if (discount > 0) {
                    discountRow.classList.remove('d-none');
                    cartDiscount.textContent = '-{{ $currencySymbol }}' + discount.toFixed(2);
                } else {
                    discountRow.classList.add('d-none');
                }
                
                // Update summary display
                summarySubtotal.textContent = '{{ $currencySymbol }}' + subtotal.toFixed(2);
                summaryDiscount.textContent = '-{{ $currencySymbol }}' + discount.toFixed(2);
                summaryTotal.textContent = '{{ $currencySymbol }}' + total.toFixed(2);
                
                // Update hidden inputs
                subtotalInput.value = subtotal.toFixed(2);
                discountAmountInput.value = discount.toFixed(2);
                totalAmountInput.value = total.toFixed(2);
                
                completeSaleBtn.disabled = false;
                
                // Add event listeners to quantity controls
                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('change', function() {
                        const index = parseInt(this.dataset.index);
                        const newQuantity = parseInt(this.value);
                        
                        if (newQuantity < 1) {
                            this.value = 1;
                            return;
                        }
                        
                        // Ensure price is a number
                        const price = typeof cart[index].price === 'string' ? parseFloat(cart[index].price) : cart[index].price;
                        
                        cart[index].quantity = newQuantity;
                        cart[index].total = newQuantity * price;
                        updateCartDisplay();
                    });
                });
                
                document.querySelectorAll('.increase-qty').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.dataset.index);
                        
                        // Ensure price is a number
                        const price = typeof cart[index].price === 'string' ? parseFloat(cart[index].price) : cart[index].price;
                        
                        cart[index].quantity += 1;
                        cart[index].total = cart[index].quantity * price;
                        updateCartDisplay();
                    });
                });
                
                document.querySelectorAll('.decrease-qty').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.dataset.index);
                        if (cart[index].quantity > 1) {
                            // Ensure price is a number
                            const price = typeof cart[index].price === 'string' ? parseFloat(cart[index].price) : cart[index].price;
                            
                            cart[index].quantity -= 1;
                            cart[index].total = cart[index].quantity * price;
                            updateCartDisplay();
                        }
                    });
                });
                
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.dataset.index);
                        const productName = cart[index].name;
                        cart.splice(index, 1);
                        updateCartDisplay();
                        showAlert(`"${productName}" removed from cart`, 'info');
                    });
                });
            }
        }

        // Discount functionality
        discountInput.addEventListener('input', function() {
            updateCartDisplay();
        });

        // Clear cart functionality
        clearCartBtn.addEventListener('click', function() {
            if (cart.length > 0) {
                if (confirm('Are you sure you want to clear all items from the cart?')) {
                    cart = [];
                    discountInput.value = '0';
                    updateCartDisplay();
                    showAlert('Cart cleared', 'info');
                }
            }
        });

        // Form submission - UPDATED FOR MANUAL PRODUCTS
        saleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (cart.length === 0) {
                showAlert('Please add at least one product', 'warning');
                return;
            }
            
            // Prepare items for submission - MODIFIED FOR MANUAL PRODUCTS
            const items = cart.map(item => {
                // For manual products, we need to send all details
                if (item.isManual) {
                    return {
                        is_manual: true,
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity
                    };
                } else {
                    // For database products
                    return {
                        is_manual: false,
                        product_id: item.id,
                        quantity: item.quantity
                    };
                }
            });
            
            // Add hidden inputs for cart items - MODIFIED APPROACH
            document.querySelectorAll('input[name^="items"]').forEach(input => input.remove());
            
            items.forEach((item, index) => {
                if (item.is_manual) {
                    // Add manual product fields
                    createHiddenInput(`items[${index}][is_manual]`, '1');
                    createHiddenInput(`items[${index}][name]`, item.name);
                    createHiddenInput(`items[${index}][price]`, item.price);
                    createHiddenInput(`items[${index}][quantity]`, item.quantity);
                } else {
                    // Add database product fields
                    createHiddenInput(`items[${index}][is_manual]`, '0');
                    createHiddenInput(`items[${index}][product_id]`, item.product_id);
                    createHiddenInput(`items[${index}][quantity]`, item.quantity);
                }
            });
            
            // Submit form
            this.submit();
        });

        // Helper function to create hidden inputs
        function createHiddenInput(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            saleForm.appendChild(input);
        }

        function showAlert(message, type) {
            // Remove any existing alerts
            const existingAlert = document.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
            
            // Auto dismiss after 3 seconds
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 3000);
        }

        // After successful form submission
        @if(session('success') && session('sale_id'))
            // Set print receipt URL
            document.getElementById('print-receipt-btn').href = "{{ route('sales.receipt', ['sale' => session('sale_id')]) }}";
            // Set modal total amount
            document.getElementById('modal-total-amount').textContent = "{{ session('total_amount') ? $currencySymbol . number_format(session('total_amount'), 2) : $currencySymbol . '0.00' }}";
            // Show modal
            printModal.show();
        @endif
    });
</script>
@endsection