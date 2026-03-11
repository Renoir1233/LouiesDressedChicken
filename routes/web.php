<?php
// routes/web.php

use App\Http\Controllers\SupplierController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Redirect root based on authentication
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile']);
    
    // User Management Routes (Admin only)
    Route::middleware('permission:users.*')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    });
    
    // Role Management Routes (Super Admin only)
    Route::middleware('permission:users.*')->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Audit Log Routes (Admin/Manager)
    Route::middleware('permission:audit-logs.*')->prefix('audit-logs')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::get('/export/csv', [AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::get('/stats/dashboard', [AuditLogController::class, 'dashboardStats'])->name('audit-logs.stats');
    });
    
    // Order Routes (Cashier/Manager/Admin)
    Route::middleware('permission:orders.*')->prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/products', [OrderController::class, 'getProducts'])->name('orders.products');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');

        // Enhanced customer search and order management (move above /{id})
        Route::get('/search-customers', [OrderController::class, 'searchCustomers'])->name('orders.search-customers');
        Route::get('/search-customers-enhanced', [OrderController::class, 'searchCustomersEnhanced'])
            ->name('orders.search-customers.enhanced');
        Route::get('/customer-pending-orders', [OrderController::class, 'getCustomerPendingOrders'])
            ->name('orders.customer-pending-orders');
        // Removed duplicate add-items-existing route to avoid confusion. Use only /orders/{id}/add-items for adding items to existing orders.

        Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{id}/print', [OrderController::class, 'printReceipt'])->name('orders.print');
        Route::get('/{id}/pdf', [OrderController::class, 'generatePdfReceipt'])->name('orders.pdf');

        // Pending orders management
        Route::get('/pending/list', [OrderController::class, 'getPendingOrders'])->name('orders.pending');
        Route::post('/{id}/complete', [OrderController::class, 'completeOrder'])->name('orders.complete');
        Route::delete('/{id}/delete-pending', [OrderController::class, 'deletePendingOrder'])->name('orders.delete-pending');
        Route::post('/{id}/add-items', [OrderController::class, 'addItemsToOrder'])->name('orders.add-items');

        // Order management
        Route::get('/{id}/details', [OrderController::class, 'getOrderDetails'])->name('orders.details');
        Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
        Route::get('/{id}/audit-trail', [OrderController::class, 'getOrderAuditTrail'])->name('orders.audit-trail');

        // Search and lists
        Route::get('/list/all', [OrderController::class, 'listOrders'])->name('orders.list');
        Route::get('/search/orders', [OrderController::class, 'searchOrders'])->name('orders.search');

        // Order statistics
        Route::get('/stats/summary', [OrderController::class, 'getOrderStats'])->name('orders.stats');
    });
    
    // Billing Routes (Cashier/Manager/Admin)
    Route::middleware('permission:billing.*')->prefix('billing')->group(function () {
        // Receipt generation route
        Route::get('/receipt/{payment}', [BillingController::class, 'generateReceipt'])->name('billing.generate-receipt');
        Route::get('/', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/pending-orders', [BillingController::class, 'getPendingOrders'])->name('billing.pending-orders');
        
        // Completed and all orders
        Route::get('/completed-orders', [BillingController::class, 'getCompletedOrders'])->name('billing.completed-orders');
        Route::get('/all-orders', [BillingController::class, 'getAllOrders'])->name('billing.all-orders');
        
        // Payment processing
        Route::post('/{order}/pay', [BillingController::class, 'payOrder'])->name('billing.pay-order');
        Route::post('/{order}/full-payment', [BillingController::class, 'processFullPayment'])->name('billing.full-payment');
        
        // Reports and dashboard
        Route::get('/reports', [BillingController::class, 'reports'])->name('billing.reports');
        Route::get('/print-report', [BillingController::class, 'printReport'])->name('billing.print-report');
        Route::post('/fix-order-totals', [BillingController::class, 'fixOrderTotals'])->name('billing.fix-order-totals');

        // Order payments and history
        Route::get('/{order}/payments', [BillingController::class, 'getOrderPayments'])->name('billing.order-payments');
        Route::get('/{order}/payment-history', [BillingController::class, 'getPaymentHistory'])->name('billing.payment-history');
        
        // Dashboard and cancellation routes
        Route::get('/dashboard-stats', [BillingController::class, 'getDashboardStats'])->name('billing.dashboard-stats');
        Route::post('/{order}/cancel', [BillingController::class, 'cancelOrder'])->name('billing.cancel-order');
        Route::post('/{order}/complete', [BillingController::class, 'completeOrder'])->name('billing.complete-order');
        
        // Order details
        Route::get('/{order}/details', [BillingController::class, 'getOrderDetails'])->name('billing.order-details');
        
        // Recent transactions
        Route::get('/recent-transactions', [BillingController::class, 'getRecentTransactions'])->name('billing.recent-transactions');
    });
    
    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/sales-pdf', [OrderController::class, 'generateSalesReport'])->name('reports.sales.pdf');
        Route::get('/inventory-pdf', [OrderController::class, 'generateInventoryReport'])->name('reports.inventory.pdf');
        Route::get('/daily-sales-pdf', [OrderController::class, 'generateDailySalesSummary'])->name('reports.daily-sales.pdf');
        Route::get('/export-orders-csv', [OrderController::class, 'exportOrdersToCsv'])->name('reports.export.orders.csv');
    });
    
    // Employees Routes (Manager/Admin)
    Route::middleware('permission:employees.*')->prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{id}/get-employee', [EmployeeController::class, 'getEmployee'])->name('employees.get-employee');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
    
    // Suppliers Routes (Manager/Admin)
    Route::middleware('permission:suppliers.*')->prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/{id}/get-supplier', [SupplierController::class, 'getSupplier'])->name('suppliers.get-supplier');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });
    
    // ----------------------------------------------------------------------
    // Inventory Routes (Manager/Admin/Staff) - FIXED ORDERING APPLIED HERE
    // ----------------------------------------------------------------------
    Route::middleware('permission:inventory.*')->prefix('inventory')->group(function () {
        
        // 1. Stock Operations (POST/Specific Actions) - MUST BE FIRST
        // This ensures POST /inventory/{id}/stock-in is recognized before the GET /{inventory} route
        Route::post('/{inventory}/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/{inventory}/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('/{inventory}/toggle-status', [InventoryController::class, 'toggleStatus'])->name('inventory.toggle-status');
        Route::post('/bulk-stock-update', [InventoryController::class, 'bulkStockUpdate'])->name('inventory.bulk-update');
        Route::post('/batch/{batch}/adjust', [InventoryController::class, 'adjustBatch'])->name('inventory.adjust-batch');

        // 2. Custom GET Routes (Fixed paths and specific parameter paths)
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
        Route::get('/out-of-stock', [InventoryController::class, 'outOfStock'])->name('inventory.out-of-stock');
        Route::get('/stock-movement', [InventoryController::class, 'stockMovementHistory'])->name('inventory.stock-movement');
        Route::get('/export/audit', [InventoryController::class, 'exportWithAudit'])->name('inventory.export.audit');
        Route::get('/stats/dashboard', [InventoryController::class, 'dashboardStats'])->name('inventory.stats');
        Route::get('/expiring-soon', [InventoryController::class, 'expiringSoon'])->name('inventory.expiring-soon');
        Route::get('/report', [InventoryController::class, 'inventoryReport'])->name('inventory.report');
        Route::get('/report/export', [InventoryController::class, 'exportInventoryReport'])->name('inventory.report.export');

        // New stock management & export routes
        Route::get('/stock-in', [StockInController::class, 'index'])->name('inventory.stock-in.index');
        Route::post('/stock-in/store', [StockInController::class, 'store'])->name('inventory.stock-in.store');
        Route::get('/stock-out', [StockOutController::class, 'index'])->name('inventory.stock-out.index');
        Route::post('/stock-out/store', [StockOutController::class, 'store'])->name('inventory.stock-out.store');
        Route::get('/movement-history', [InventoryController::class, 'stockMovementHistory'])->name('inventory.movement-history');
        Route::get('/stock-in/export', [InventoryController::class, 'exportStockIns'])->name('inventory.stock-in.export');
        Route::get('/stock-out/export', [InventoryController::class, 'exportStockOuts'])->name('inventory.stock-out.export');
        Route::get('/inventory/export', [InventoryController::class, 'exportInventory'])->name('inventory.export');

        // Print routes
        Route::get('/print/low-stock', [InventoryController::class, 'printLowStock'])->name('inventory.print.low-stock');
        Route::get('/print/expiring', [InventoryController::class, 'printExpiring'])->name('inventory.print.expiring');

        // Routes with parameters that clash with {inventory} - placed before resource routes
        Route::get('/{inventory}/history', [InventoryController::class, 'getHistory'])->name('inventory.history');
        Route::get('/{inventory}/fifo-batches', [InventoryController::class, 'getFifoBatches'])->name('inventory.fifo-batches');
        Route::get('/batch/{batch}', [InventoryController::class, 'showBatch'])->name('inventory.show-batch');

        // 3. Resource routes (The "catch-all" routes - MUST BE LAST)
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/{id}/get-product', [InventoryController::class, 'getProduct'])->name('inventory.get-product');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});