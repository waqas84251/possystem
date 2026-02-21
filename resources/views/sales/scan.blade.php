@extends('layouts.app')

@section('title', '12-Digit Barcode Scanner - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">12-Digit Barcode Scanner</h1>
        <div>
            <a href="{{ route('sales.create') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Manual Entry
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> View Sales
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Scanner Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">12-Digit Barcode Scanner</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Position the 12-digit barcode in front of the camera. Only valid 12-digit codes will be accepted.
                    </div>

                    <div class="scanner-container text-center mb-4">
                        <div id="reader" class="border rounded p-2 mb-3" style="height: 300px; background-color: #f8f9fa; position: relative;">
                            <div class="d-flex align-items-center justify-content-center h-100" id="camera-placeholder">
                                <div class="text-center">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Initializing camera...</p>
                                </div>
                            </div>
                            <div class="scanner-laser" style="height: 2px; background-color: red; width: 100%; position: absolute; top: 50%; opacity: 0.5; animation: scan 2s infinite; display: none;"></div>
                            <!-- Scanner overlay with targeting box -->
                            <div class="scanner-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
                                <div class="scanner-target" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 200px; height: 100px; border: 2px solid rgba(0, 255, 0, 0.7); box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);"></div>
                            </div>
                        </div>
                        
                        <div class="scanner-controls mb-3">
                            <select class="form-select d-inline-block me-2" id="camera-select" style="width: auto;">
                                <option value="">Select Camera</option>
                            </select>
                            <button class="btn btn-outline-secondary" id="switch-camera">
                                <i class="fas fa-sync-alt me-1"></i> Switch Camera
                            </button>
                            <button class="btn btn-outline-secondary" id="toggle-camera">
                                <i class="fas fa-power-off me-1"></i> Stop Camera
                            </button>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small">Scan Threshold:
                                    <input type="number" class="form-control form-control-sm d-inline-block" id="threshold-input" value="3" min="1" max="6" style="width: 70px; margin-left: 6px"/>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Manual Entry Form -->
                        <div class="manual-entry mt-4 p-3 border rounded">
                            <h6 class="mb-3">Manual Barcode Entry</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" id="manual-barcode" placeholder="Enter 12-digit barcode" maxlength="12" pattern="[0-9]{12}">
                                <button class="btn btn-primary" type="button" id="manual-submit">
                                    <i class="fas fa-keyboard me-1"></i> Add Manually
                                </button>
                            </div>
                            <div class="form-text">Enter exactly 12 digits (numbers only)</div>
                        </div>
                        
                        <!-- Manual Product Entry Section -->
                        <div class="manual-product-entry mt-4 p-3 border rounded">
                            <h6 class="mb-3">Manual Product Entry</h6>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label for="manual-product-name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="manual-product-name" placeholder="Enter product name">
                                </div>
                                <div class="col-md-3">
                                    <label for="manual-product-price" class="form-label">Price ({{ $currencySymbol }})</label>
                                    <input type="number" class="form-control" id="manual-product-price" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label for="manual-product-quantity" class="form-label">Qty</label>
                                    <input type="number" class="form-control" id="manual-product-quantity" value="1" min="1">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" type="button" id="add-manual-product">
                                        <i class="fas fa-plus me-1"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="scan-status mt-2 p-2 bg-light rounded small" id="scan-status">
                            Idle. Click Start and center code inside viewer.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Current Receipt</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="scanned-products-table">
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
                                    <td colspan="5" class="text-center text-muted py-4">No products scanned yet</td>
                                </tr>
                            </tbody>
                            <tfoot id="scanned-products-footer" class="d-none">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td colspan="2" class="fw-bold" id="cart-subtotal">{{ $currencySymbol }}0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tax (0%):</td>
                                    <td colspan="2" class="fw-bold" id="cart-tax">{{ $currencySymbol }}0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td colspan="2" class="fw-bold" id="cart-total">{{ $currencySymbol }}0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Sale Form -->
                    <form id="sale-form" action="{{ route('sales.store') }}" method="POST" class="p-3 border-top">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer (Optional)</label>
                            <select class="form-select" id="customer_id" name="customer_id">
                                <option value="">Walk-in Customer</option>
                                @isset($customers)
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                @else
                                    <option value="">No customers available</option>
                                @endisset
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

                        <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                        <input type="hidden" name="discount_amount" id="discount-amount-input" value="0">
                        <input type="hidden" name="total_amount" id="total-amount-input" value="0">

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
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="complete-sale-btn" disabled>
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



<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p id="status-message">Initializing scanner...</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scanner Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <h4 id="error-message">Camera Access Denied</h4>
                <p class="mb-0" id="error-details">Please allow camera access to use the barcode scanner.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @keyframes scan {
        0% { transform: translateY(-100px); }
        50% { transform: translateY(100px); }
        100% { transform: translateY(-100px); }
    }
    
    .scanner-container {
        position: relative;
    }
    
    #reader {
        position: relative;
        overflow: hidden;
    }
    
    #reader video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .scanned-product-row {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .scan-status {
        padding: 6px 10px;
        border-radius: 6px;
        background: #1e293b;
        color: #e6eef6;
        margin-bottom: 8px;
        font-size: 13px;
    }
    
    /* Scanner targeting box */
    .scanner-target {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 100px;
        border: 2px solid rgba(0, 255, 0, 0.7);
        box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        pointer-events: none;
    }
    
    .scanner-target::before, .scanner-target::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(0, 255, 0, 0.7);
    }
    
    .scanner-target::before {
        top: -5px;
        left: -5px;
        border-right: none;
        border-bottom: none;
    }
    
    .scanner-target::after {
        bottom: -5px;
        right: -5px;
        border-left: none;
        border-top: none;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .scanner-controls {
            flex-direction: column;
            gap: 10px;
        }
        
        .scanner-controls .btn,
        .scanner-controls select {
            width: 100%;
            margin: 5px 0;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        #reader {
            height: 250px !important;
        }
        
        .scanner-target {
            width: 150px;
            height: 75px;
        }
    }
</style>
@endsection

@section('scripts')
<!-- Include html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let cart = [];
        let manualProductId = 9000000; // Starting ID for manual products
        const scannedProductsBody = document.getElementById('scanned-products-body');
        const scannedProductsFooter = document.getElementById('scanned-products-footer');
        const cartSubtotal = document.getElementById('cart-subtotal');
        const cartTotal = document.getElementById('cart-total');
        const completeSaleBtn = document.getElementById('complete-sale-btn');
        const clearCartBtn = document.getElementById('clear-cart-btn');
        const toggleCameraBtn = document.getElementById('toggle-camera');
        const switchCameraBtn = document.getElementById('switch-camera');
        const cameraSelect = document.getElementById('camera-select');
        const thresholdInput = document.getElementById('threshold-input');
        const scanStatusEl = document.getElementById('scan-status');
        const manualBarcodeInput = document.getElementById('manual-barcode');
        const manualSubmitBtn = document.getElementById('manual-submit');
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        const subtotalInput = document.getElementById('subtotal-input');
        const discountInput = document.getElementById('discount_amount');
        const discountAmountInput = document.getElementById('discount-amount-input');
        const totalAmountInput = document.getElementById('total-amount-input');
        const saleForm = document.getElementById('sale-form');
        const summarySubtotal = document.getElementById('summary-subtotal');
        const summaryDiscount = document.getElementById('summary-discount');
        const summaryTotal = document.getElementById('summary-total');
        const manualProductNameInput = document.getElementById('manual-product-name');
        const manualProductPriceInput = document.getElementById('manual-product-price');
        const manualProductQuantityInput = document.getElementById('manual-product-quantity');
        const addManualProductBtn = document.getElementById('add-manual-product');
        
        let html5QrCode = null;
        let cameras = [];
        let currentCameraId = null;
        let isScanning = false;
        let recentResults = [];
        const BUFFER_MS = 1500;
        let lastAccepted = null;

        // Update scan status
        function updateScanStatus(message, type = 'info') {
            scanStatusEl.textContent = message;
            let bgColor = 'bg-light';
            let textColor = 'text-dark';
            
            if (type === 'error') {
                bgColor = 'bg-danger';
                textColor = 'text-white';
            } else if (type === 'success') {
                bgColor = 'bg-success';
                textColor = 'text-white';
            }
            
            scanStatusEl.className = `scan-status mt-2 p-2 rounded small ${bgColor} ${textColor}`;
        }

        // Show error modal with custom message
        function showErrorModal(title, message, details = '') {
            document.getElementById('error-message').textContent = title;
            document.getElementById('error-details').textContent = message + (details ? ' Details: ' + details : '');
            errorModal.show();
        }

        // Initialize the scanner
        async function initScanner() {
            try {
                statusModal.show();
                document.getElementById('status-message').textContent = "Loading cameras...";
                
                // Get available cameras
                const devices = await Html5Qrcode.getCameras();
                cameras = devices;
                
                if (cameras.length === 0) {
                    document.getElementById('camera-placeholder').innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-camera-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No cameras found</p>
                            <button class="btn btn-sm btn-primary mt-2" id="retry-camera-btn">
                                <i class="fas fa-redo me-1"></i> Retry
                            </button>
                        </div>
                    `;
                    
                    // Add event listener to retry button
                    document.getElementById('retry-camera-btn').addEventListener('click', initScanner);
                    
                    statusModal.hide();
                    updateScanStatus('No cameras found. Please connect a camera and retry.', true);
                    showErrorModal('No Camera Found', 'No cameras were detected on your device.', 'Please connect a camera and click the retry button.');
                    return;
                }
                
                // Populate camera select
                cameraSelect.innerHTML = '<option value="">Select Camera</option>';
                cameras.forEach((camera, index) => {
                    const label = camera.label || `Camera ${index + 1}`;
                    cameraSelect.innerHTML += `<option value="${camera.id}">${label}</option>`;
                });
                
                if (cameras.length > 0) {
                    // Try to find back camera first
                    const backCamera = cameras.find(cam => /back|rear|environment/i.test(cam.label));
                    currentCameraId = backCamera ? backCamera.id : cameras[0].id;
                    cameraSelect.value = currentCameraId;
                    
                    statusModal.hide();
                    await startScanner();
                }
            } catch (error) {
                console.error('Error initializing scanner:', error);
                statusModal.hide();
                
                let errorMessage = 'Error initializing scanner';
                let errorDetails = error.message;
                
                if (error.message.includes('NotAllowedError')) {
                    errorMessage = 'Camera Access Denied';
                    errorDetails = 'Please allow camera access in your browser settings to use the barcode scanner.';
                } else if (error.message.includes('NotFoundError')) {
                    errorMessage = 'No Camera Available';
                    errorDetails = 'No camera was found on your device. Please connect a camera and try again.';
                }
                
                showErrorModal(errorMessage, errorDetails);
                updateScanStatus('Error accessing camera', true);
                document.getElementById('camera-placeholder').innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p class="text-danger">Error accessing camera</p>
                        <button class="btn btn-sm btn-primary mt-2" id="retry-error-btn">
                            <i class="fas fa-redo me-1"></i> Retry
                        </button>
                    </div>
                `;
                
                // Add event listener to retry button
                document.getElementById('retry-error-btn').addEventListener('click', initScanner);
            }
        }

        // Start the scanner
        async function startScanner() {
            if (!currentCameraId) return;
            
            try {
                // Create the scanner if it doesn't exist
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader", false);
                }
                
                // Hide placeholder and show laser
                document.getElementById('camera-placeholder').style.display = 'none';
                document.querySelector('.scanner-laser').style.display = 'block';
                
                const fps = 15;
                const qrbox = computeQrBox();
                
                // Start scanning
                await html5QrCode.start(
                    currentCameraId,
                    {
                        fps: fps,
                        qrbox: qrbox,
                        disableFlip: false,
                        rememberLastUsedCamera: true,
                        videoConstraints: { 
                            deviceId: { exact: currentCameraId },
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                            focusMode: 'continuous'
                        }
                    },
                    onScanSuccess,
                    onScanFailure
                );
                
                isScanning = true;
                toggleCameraBtn.innerHTML = '<i class="fas fa-power-off me-1"></i> Stop Camera';
                toggleCameraBtn.classList.remove('btn-outline-secondary');
                toggleCameraBtn.classList.add('btn-warning');
                
                updateScanStatus("Scanning — center 12-digit barcode inside the box.");
                
            } catch (error) {
                console.error('Error starting scanner:', error);
                showErrorModal('Scanner Error', 'Failed to start the scanner.', error.message);
                updateScanStatus('Error starting scanner', true);
            }
        }

        // Compute QR box size
        function computeQrBox() {
            return { width: 200, height: 100 }; // Fixed size for better accuracy
        }

        // Stop the scanner
        async function stopScanner() {
            if (html5QrCode && isScanning) {
                try {
                    await html5QrCode.stop();
                    html5QrCode.clear();
                } catch (error) {
                    console.error('Error stopping scanner:', error);
                }
            }
            
            document.getElementById('camera-placeholder').style.display = 'flex';
            document.querySelector('.scanner-laser').style.display = 'none';
            isScanning = false;
            toggleCameraBtn.innerHTML = '<i class="fas fa-power-off me-1"></i> Start Camera';
            toggleCameraBtn.classList.remove('btn-warning');
            toggleCameraBtn.classList.add('btn-outline-secondary');
            updateScanStatus("Scanner stopped. Click Start to scan again.");
        }

        // Strict 12-digit normalization
        function normalizeUPC(decodedText, format) {
            let txt = decodedText.trim();
            // Remove any non-digit characters
            txt = txt.replace(/\D/g,'');
            // Enforce exactly 12 digits
            if(txt.length > 12) txt = txt.slice(0,12);
            else if(txt.length < 12) return null; // ignore incomplete
            return txt;
        }

        // Handle successful scan
        function onScanSuccess(decodedText, decodedResult) {
            const now = Date.now();
            const format = decodedResult?.result?.format?.formatName ?? decodedResult?.format ?? "CODE";
            const cleaned = normalizeUPC(decodedText, format);
            
            if(!cleaned) {
                updateScanStatus("Invalid code: Must be exactly 12 digits", true);
                return; // ignore incomplete codes
            }

            recentResults.push({text: cleaned, t: now, fmt: format});
            recentResults = recentResults.filter(r => now - r.t <= BUFFER_MS);

            const threshold = Math.max(1, Math.min(6, Number(thresholdInput.value)||3));
            const count = recentResults.filter(r => r.text===cleaned).length;

            updateScanStatus(`Scanning: ${cleaned} (${count}/${threshold} matches)`);

            if(count >= threshold && lastAccepted !== cleaned) {
                lastAccepted = cleaned;
                scanBarcode(cleaned);
                
                // Pause briefly to prevent multiple scans of the same code
                html5QrCode.pause();
                setTimeout(() => {
                    if (html5QrCode && isScanning) {
                        html5QrCode.resume();
                    }
                }, 1000);
            }
        }

        // Handle scan failure (most failures are expected)
        function onScanFailure(error) {
            // Most errors are just because a code wasn't found, which is normal
            // Only log actual errors
            if (!error.includes('No MultiFormat Readers')) {
                console.log('Scan error:', error);
            }
        }

        // Camera selection change
        cameraSelect.addEventListener('change', function() {
            if (this.value) {
                currentCameraId = this.value;
                if (isScanning) {
                    stopScanner();
                    setTimeout(startScanner, 500);
                }
            }
        });

        // Switch camera button
        switchCameraBtn.addEventListener('click', function() {
            if (cameras.length <= 1) {
                showAlert('Only one camera available', 'info');
                return;
            }
            
            const currentIndex = cameras.findIndex(cam => cam.id === currentCameraId);
            const nextIndex = (currentIndex + 1) % cameras.length;
            currentCameraId = cameras[nextIndex].id;
            cameraSelect.value = currentCameraId;
            
            if (isScanning) {
                stopScanner();
                setTimeout(startScanner, 500);
            }
        });

        // Toggle camera button
        toggleCameraBtn.addEventListener('click', function() {
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        });

        // Manual barcode submission
        manualSubmitBtn.addEventListener('click', function() {
            const barcode = manualBarcodeInput.value.trim();
            if (!barcode) {
                showAlert('Please enter a barcode', 'warning');
                return;
            }
            
            if (!/^\d{12}$/.test(barcode)) {
                showAlert('Barcode must be exactly 12 digits', 'warning');
                return;
            }
            
            scanBarcode(barcode);
            manualBarcodeInput.value = '';
        });

        // Allow Enter key to submit manual barcode
        manualBarcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                manualSubmitBtn.click();
            }
        });

        // Manual product entry functionality
        addManualProductBtn.addEventListener('click', addManualProduct);
        
        manualProductNameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addManualProduct();
            }
        });
        
        manualProductPriceInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addManualProduct();
            }
        });

        function addManualProduct() {
            const name = manualProductNameInput.value.trim();
            const price = parseFloat(manualProductPriceInput.value);
            const quantity = parseInt(manualProductQuantityInput.value) || 1;
            
            if (!name) {
                showAlert('Please enter a product name', 'warning');
                manualProductNameInput.focus();
                return;
            }
            
            if (isNaN(price) || price <= 0) {
                showAlert('Please enter a valid price', 'warning');
                manualProductPriceInput.focus();
                return;
            }
            
            if (isNaN(quantity) || quantity < 1) {
                showAlert('Please enter a valid quantity', 'warning');
                manualProductQuantityInput.focus();
                return;
            }
            
            // Create a manual product object
            const manualProduct = {
                id: 'manual_' + manualProductId++, // Use a unique ID for manual products
                name: name,
                price: price,
                quantity: quantity,
                total: price * quantity,
                isManual: true // Flag to identify manual products
            };
            
            addProductToCart(manualProduct);
            
            // Reset form
            manualProductNameInput.value = '';
            manualProductPriceInput.value = '';
            manualProductQuantityInput.value = '1';
            manualProductNameInput.focus();
        }

        // Barcode scanning function - CONNECTED TO YOUR DATABASE
        function scanBarcode(barcode) {
            if (!barcode) {
                showAlert('Invalid barcode', 'warning');
                return;
            }
            
            // Validate it's exactly 12 digits
            if (!/^\d{12}$/.test(barcode)) {
                showAlert('Invalid barcode: Must be exactly 12 digits', 'warning');
                return;
            }
            
            console.log('Scanning 12-digit barcode:', barcode);
            
            // Show loading status
            updateScanStatus(`Looking up product for barcode: ${barcode}...`);
            
            // Make AJAX request to find product by barcode - USING YOUR ACTUAL DATABASE
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
                    
                    updateScanStatus(`Added: ${data.product.name}`, 'success');
                    
                    // Reset status after 2 seconds
                    setTimeout(() => {
                        if (isScanning) {
                            updateScanStatus("Scanning — center 12-digit barcode inside the box.");
                        }
                    }, 2000);
                } else {
                    showAlert('Product not found in database for barcode: ' + barcode, 'danger');
                    updateScanStatus('Product not found: ' + barcode, 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching product:', error);
                showAlert('Error connecting to database', 'danger');
                updateScanStatus('Database error', 'error');
            });
        }

        function addProductToCart(product) {
            // Convert price to number to avoid toFixed() error
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
                    price: price,
                    quantity: product.quantity || 1,
                    total: (price * (product.quantity || 1)),
                    isManual: product.isManual || false
                });
            }
            
            updateCartDisplay();
        }

        function updateCartDisplay() {
            if (cart.length === 0) {
                scannedProductsBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No products scanned yet</td></tr>';
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
    
    document.querySelector('.card-body').insertBefore(alert, document.querySelector('.card-body').firstChild);
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 3000);
}}}

// Initialize scanner on page load
initScanner();
    });
</script>
@endsection