<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $product->name }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛒</text></svg>">
    <style>
        @media print {
            body, html {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .barcode-label {
                width: 2.5in;
                height: 1.5in;
                padding: 10px;
                border: 1px solid #000;
                text-align: center;
                display: inline-block;
                margin: 5px;
                page-break-inside: avoid;
                background: white;
            }
            .barcode-container {
                display: block !important;
                width: 100%;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .barcode-label {
            width: 2.5in;
            height: 1.5in;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
            display: inline-block;
            margin: 8px;
            page-break-inside: avoid;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .store-name {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .product-name {
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #34495e;
        }
        
        .barcode-image {
            height: 45px;
            display: block;
            margin: 3px auto;
            max-width: 100%;
        }
        
        .barcode-number {
            font-size: 9px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            letter-spacing: 0.5px;
            color: #7f8c8d;
            margin: 3px 0;
        }
        
        .product-price {
            font-size: 14px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 3px;
        }
        
        .product-sku {
            font-size: 9px;
            color: #95a5a6;
            margin-top: 2px;
        }
        
        .print-controls {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(2.5in, 1fr));
            gap: 15px;
            justify-content: center;
        }
        
        #printable-area {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4 no-print">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="print-controls">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Print Barcode Labels</h2>
                        <div>
                            <button onclick="preparePrint()" class="btn btn-primary">
                                <i class="fas fa-print"></i> Print Labels
                            </button>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Product
                            </a>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Product Information</h6>
                                    <p class="mb-1"><strong>Name:</strong> {{ $product->name }}</p>
                                    <p class="mb-1"><strong>SKU:</strong> {{ $product->sku }}</p>
                                    <p class="mb-1"><strong>Price:</strong> {{ $currencySymbol }}{{ number_format($product->price, 2) }}</p>
                                    <p class="mb-0"><strong>Barcode:</strong> {{ $product->barcode }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Print Settings</h6>
                                    <div class="mb-3">
                                        <label for="copies" class="form-label">Number of copies:</label>
                                        <input type="number" id="copies" class="form-control" value="1" min="1" max="100">
                                    </div>
                                    <div class="mb-3">
                                        <label for="labelsPerPage" class="form-label">Labels per page:</label>
                                        <select id="labelsPerPage" class="form-select">
                                            <option value="9">9 labels (3×3)</option>
                                            <option value="12">12 labels (3×4)</option>
                                            <option value="16">16 labels (4×4)</option>
                                            <option value="20">20 labels (4×5)</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-success" onclick="generateBarcodes()">
                                        <i class="fas fa-sync"></i> Generate Barcodes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="barcode-container" class="barcode-grid">
        <!-- Barcodes will be generated here -->
    </div>
    
    <!-- Hidden printable area -->
    <div id="printable-area"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            generateBarcodes();
        });

        function generateBarcodes() {
            const copies = parseInt(document.getElementById('copies').value) || 1;
            const labelsPerPage = parseInt(document.getElementById('labelsPerPage').value) || 9;
            const container = document.getElementById('barcode-container');
            const printableArea = document.getElementById('printable-area');
            
            container.innerHTML = '';
            printableArea.innerHTML = '';
            
            // Calculate grid layout based on labels per page
            let gridColumns;
            switch(labelsPerPage) {
                case 9:
                    gridColumns = 'repeat(3, 1fr)';
                    break;
                case 12:
                    gridColumns = 'repeat(3, 1fr)';
                    break;
                case 16:
                    gridColumns = 'repeat(4, 1fr)';
                    break;
                case 20:
                    gridColumns = 'repeat(4, 1fr)';
                    break;
                default:
                    gridColumns = 'repeat(3, 1fr)';
            }
            
            container.style.gridTemplateColumns = gridColumns;
            
            // Create barcode labels
            for (let i = 0; i < copies; i++) {
                const label = createBarcodeLabel();
                container.appendChild(label.cloneNode(true));
                
                // Also add to printable area
                const printLabel = createBarcodeLabel();
                printableArea.appendChild(printLabel);
            }
        }
        
        function createBarcodeLabel() {
            const label = document.createElement('div');
            label.className = 'barcode-label';
            
            // Use the actual barcode SVG from the server
            const barcodeSvg = `{!! $barcodeSvg !!}`;
            
            label.innerHTML = `
                <div class="store-name">{{ $businessName }}</div>
                <div class="product-name">{{ $product->name }}</div>
                <div class="barcode-image">${barcodeSvg}</div>
                <div class="barcode-number">{{ $product->barcode }}</div>
                <div class="product-price">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</div>
                <div class="product-sku">SKU: {{ $product->sku }}</div>
            `;
            
            return label;
        }
        
        function preparePrint() {
            const printableArea = document.getElementById('printable-area');
            
            // Show the printable area temporarily
            printableArea.style.display = 'block';
            
            // Print the document
            window.print();
            
            // Hide the printable area again
            setTimeout(() => {
                printableArea.style.display = 'none';
            }, 100);
        }
    </script>

    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</body>
</html>