<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

// Public Routes
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Product Public Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search-suggestions');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show')->where('product', '[0-9]+');
Route::get('/products/low-stock-items', [DashboardController::class, 'lowStock'])->name('products.low-stock');

// Category Public Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->where('category', '[0-9]+');

// Inventory Public Routes
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/history/{product}', [InventoryController::class, 'stockHistory'])->name('inventory.stock-history');
});

// Customer Public Routes
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->where('customer', '[0-9]+');

// Sales Public Routes
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show')->where('sale', '[0-9]+');

// Report Public Routes
Route::prefix('reports')->group(function () {
    Route::get('/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/sales/export', [ReportController::class, 'exportSalesReport'])->name('reports.sales.export');
    
    Route::get('/products', [ReportController::class, 'productsReport'])->name('reports.products');
    Route::get('/products/export', [ReportController::class, 'exportProductsReport'])->name('reports.products.export');
    
    Route::get('/customers', [ReportController::class, 'customersReport'])->name('reports.customers');
    Route::post('/customers/export', [ReportController::class, 'exportCustomersReport'])->name('reports.customers.export');
    
    Route::get('/inventory', [ReportController::class, 'inventoryReport'])->name('reports.inventory');
    Route::post('/inventory/export', [ReportController::class, 'exportInventoryReport'])->name('reports.inventory.export');
});

// Settings Public Routes
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

// Help Public Routes
Route::prefix('help')->group(function () {
    Route::get('/', [HelpController::class, 'index'])->name('help.index');
    Route::get('/faq', [HelpController::class, 'faq'])->name('help.faq');
    Route::get('/documentation', [HelpController::class, 'documentation'])->name('help.documentation');
    Route::get('/contact', [HelpController::class, 'contact'])->name('help.contact');
    Route::get('/tickets', [HelpController::class, 'tickets'])->name('help.tickets');
    Route::get('/tickets/create', [HelpController::class, 'createTicket'])->name('help.tickets.create');
    Route::post('/tickets/store', [HelpController::class, 'storeTicket'])->name('help.tickets.store');
    Route::get('/tickets/{ticket}', [HelpController::class, 'showTicket'])->name('help.tickets.show');
    Route::post('/tickets/{ticket}/response', [HelpController::class, 'addResponse'])->name('help.tickets.response');
});

Route::middleware(['auth'])->group(function () {
    
    // Product Routes (Protected)
    Route::resource('products', ProductController::class)->except(['index', 'show']);
  // routes/web.php
  Route::post('/products/{product}/restock', [ProductController::class, 'quickRestock'])->name('products.restock');
  Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');

    // Barcode routes
Route::get('/sales/scan', [SaleController::class, 'scan'])->name('sales.scan');
    Route::get('/products/by-barcode/{barcode}', [ProductController::class, 'findByBarcode']);
Route::post('/sales/scan-barcode', [SaleController::class, 'fetchProductByBarcode'])->name('sales.scan-barcode');
    // Route::get('/barcode-scanner', [BarcodeScannerController::class, 'index'])->name('barcode.scanner');
    Route::get('/barcode-scanner', [ProductController::class, 'index'])->name('barcode.scanner');
    Route::post('/products/generate-barcode', [ProductController::class, 'generateBarcode'])
        ->name('products.generate-barcode');
    Route::get('/products/barcode/print/{id}', [ProductController::class, 'printBarcode'])
        ->name('products.barcode.print');
    Route::get('/products/barcode/download/{barcode}', [ProductController::class, 'downloadBarcode'])
        ->name('products.download-barcode');
    
    // Quick stock update
    Route::post('/products/{product}/quick-update-stock', [ProductController::class, 'quickUpdateStock'])
        ->name('products.quick-update-stock');

    // Category Routes (Protected)
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);

    // Customer Routes (Protected)
    Route::resource('customers', CustomerController::class)->except(['index', 'show']);
// Sales routes (Protected)
Route::prefix('sales')->group(function () {
    Route::get('/create', [SaleController::class, 'create'])->name('sales.create');
    Route::get('/scan', [SaleController::class, 'scan'])->name('sales.scan'); // Add this line
    Route::post('/', [SaleController::class, 'store'])->name('sales.store');
    Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::get('/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::post('/scan-barcode', [SaleController::class, 'findProductByBarcode'])->name('sales.scan-barcode');
});

    // Inventory Routes (Protected)
    Route::prefix('inventory')->group(function () {
        Route::get('/adjust/{product}', [InventoryController::class, 'showAdjust'])->name('inventory.show-adjust');
        Route::post('/adjust/{product}', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    });
    
    
    // User Management Routes - Only accessible by Admins
    Route::middleware(['can:manage-users'])->group(function () {
        Route::resource('users', UserController::class);
    });
}); // Uncommented this line to protect routes



// Registration (public)
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
 Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');