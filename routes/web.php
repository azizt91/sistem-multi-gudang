<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\StockHeaderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarcodeScannerController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]); // Disable public registration

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Barcode Scanner (all authenticated users)
    Route::get('/scanner', [BarcodeScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/search', [BarcodeScannerController::class, 'search'])->name('scanner.search');

    // User Manual (all authenticated users)
    Route::get('/manual', function () {
        return view('manual.index');
    })->name('manual.index');

    // Items - Admin routes first (to prevent {item} from catching 'create')
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });

    // Items - View, search, and print barcodes (all roles) - define AFTER create route
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items/print-barcodes', [ItemController::class, 'printBarcodes'])->name('items.print-barcodes');
    Route::post('/items/find-by-code', [ItemController::class, 'findByCode'])->name('items.find-by-code');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::get('/items/{item}/barcode', [ItemController::class, 'barcode'])->name('items.barcode');

    // =====================================================
    // Stock Headers (Document-based transactions) - NEW
    // =====================================================
    
    // All authenticated users can view
    Route::get('/stock-headers', [StockHeaderController::class, 'index'])->name('stock-headers.index');
    Route::get('/stock-headers/{stockHeader}', [StockHeaderController::class, 'show'])->name('stock-headers.show');
    Route::get('/stock-headers/{stockHeader}/receipt', [StockHeaderController::class, 'receipt'])->name('stock-headers.receipt');
    Route::post('/stock-headers/{stockHeader}/signatures', [StockHeaderController::class, 'saveSignatures'])->name('stock-headers.signatures.save');
    Route::get('/stock-headers/{stockHeader}/pdf', [StockHeaderController::class, 'downloadPdf'])->name('stock-headers.pdf');

    // Admin and Staff can create transactions
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/stock-in/new', [StockHeaderController::class, 'createStockIn'])->name('stock-headers.create-in');
        Route::get('/stock-out/new', [StockHeaderController::class, 'createStockOut'])->name('stock-headers.create-out');
        Route::get('/stock-out/new', [StockHeaderController::class, 'createStockOut'])->name('stock-headers.create-out');
        Route::post('/stock-headers', [StockHeaderController::class, 'store'])->name('stock-headers.store');

        // Stock Transfer (Inter-Warehouse)
        Route::resource('stock-transfers', \App\Http\Controllers\StockTransferController::class);
    });

    // Admin only can delete
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/stock-headers/{stockHeader}', [StockHeaderController::class, 'destroy'])->name('stock-headers.destroy');
    });

    // =====================================================
    // Quick stock via barcode (Handled by StockHeaderController)
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::post('/quick-stock-in', [StockHeaderController::class, 'quickStockIn'])->name('stock-headers.quick-stock-in');
        Route::post('/quick-stock-out', [StockHeaderController::class, 'quickStockOut'])->name('stock-headers.quick-stock-out');
    });

    // Reports (Owner and Admin only)
    Route::middleware(['role:admin,owner'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/daily/pdf', [ReportController::class, 'exportDailyPdf'])->name('daily.pdf');
        Route::get('/daily/excel', [ReportController::class, 'exportDailyExcel'])->name('daily.excel');
        Route::get('/monthly/pdf', [ReportController::class, 'exportMonthlyPdf'])->name('monthly.pdf');
        Route::get('/monthly/excel', [ReportController::class, 'exportMonthlyExcel'])->name('monthly.excel');
        Route::get('/stock/excel', [ReportController::class, 'exportStockExcel'])->name('stock.excel');
        Route::get('/transactions/excel', [ReportController::class, 'exportTransactionsExcel'])->name('transactions.excel');
        Route::get('/stock/excel', [ReportController::class, 'exportStockExcel'])->name('stock.excel');
        Route::get('/transactions/excel', [ReportController::class, 'exportTransactionsExcel'])->name('transactions.excel');
    });

    // Audit Logs (Admin and Owner only)
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        // Categories
        Route::resource('categories', CategoryController::class);

        // Units
        Route::resource('units', UnitController::class);

        // Users
        Route::resource('users', UserController::class);

        // Warehouses (Multi-Warehouse)
        Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class);

        // Company Profile (Settings)
        Route::get('/company-profile', [\App\Http\Controllers\CompanyProfileController::class, 'edit'])->name('company-profile.edit');
        Route::put('/company-profile', [\App\Http\Controllers\CompanyProfileController::class, 'update'])->name('company-profile.update');

        // Delete transactions (Legacy - Disabled)
        // Route::delete('/transactions/{transaction}', [StockTransactionController::class, 'destroy'])->name('transactions.destroy');
    });
});
