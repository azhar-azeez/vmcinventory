<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Dashboards\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Order\DueOrderController;
use App\Http\Controllers\Order\OrderCompleteController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\OrderPendingController;
use App\Http\Controllers\Rent\RentalController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductExportController;
use App\Http\Controllers\Product\ProductImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('php/', function () {
    return phpinfo();
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});
    // Route for displaying the stock maintenance report form
    Route::get('/products/product-report', [ProductController::class, 'getProductStockReport'])->name('products.getProductStockReport');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard/', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    // Route::resource('/users', UserController::class); //->except(['show']);
    Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/store-settings', [ProfileController::class, 'store_settings'])->name('profile.store.settings');
    Route::post('/profile/store-settings', [ProfileController::class, 'store_settings_store'])->name('profile.store.settings.store');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/quotations', QuotationController::class);
    Route::resource('/customers', CustomerController::class);
    Route::resource('/suppliers', SupplierController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/units', UnitController::class);

    // Route Products
    Route::get('products/import/', [ProductImportController::class, 'create'])->name('products.import.view');
    Route::post('products/import/', [ProductImportController::class, 'store'])->name('products.import.store');
    Route::get('products/export/', [ProductExportController::class, 'create'])->name('products.export.store');
    Route::resource('/products', ProductController::class);

    

    // Route for generating the stock maintenance report
    Route::post('/products/generate-stock-report', [ProductController::class, 'generateStockReport'])->name('products.generateStockReport');


    // Route POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/cart/add', [PosController::class, 'addCartItem'])->name('pos.addCartItem');
    Route::post('/pos/cart/update/{rowId}', [PosController::class, 'updateCartItem'])->name('pos.updateCartItem');
    Route::delete('/pos/cart/delete/{rowId}', [PosController::class, 'deleteCartItem'])->name('pos.deleteCartItem');

    //Route::post('/pos/invoice', [PosController::class, 'createInvoice'])->name('pos.createInvoice');
    Route::post('invoice/create/', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('invoice/rent/create/', [InvoiceController::class, 'create_rent'])->name('invoice.create_rent');

    // Route Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', OrderPendingController::class)->name('orders.pending');
    Route::get('/orders/complete', OrderCompleteController::class)->name('orders.complete');

    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');


    
    // Route for displaying the profit report form
    Route::get('/orders/profit-report', [OrderController::class, 'showProfitReportForm'])->name('orders.showProfitReportForm');
    // Route for generating the profit report
    Route::post('/orders/generate-profit-report', [OrderController::class, 'generateProfitReport'])->name('orders.generateProfitReport');

    // Route for displaying the Due report form
    Route::get('/orders/duepay-report', [OrderController::class, 'getDuePaymentReport'])->name('orders.getDuePaymentReport');

    // Route for generating the Due report
    Route::post('/orders/generate-due-payments-report', [OrderController::class, 'generateDuePaymentReports'])
    ->name('orders.generateDuePaymentReports');



    //Route Rents
    Route::get('/rents', [RentalController::class, 'index'])->name('rents.index');
    Route::get('/rents/create', [RentalController::class, 'create'])->name('rents.create');
    Route::post('/rents/store', [RentalController::class, 'store'])->name('rents.store');
    Route::get('/rents/{rent}', [RentalController::class, 'show'])->name('rents.show');
    Route::delete('/rents/cancel/{rent}', [RentalController::class, 'destroy'])->name('rents.destroy');

    //Rent Invoice
    Route::get('/rents/details/{rent_id}/download', [RentalController::class, 'downloadInvoice'])->name('rent.downloadInvoice');

    // SHOW ORDER
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/cancel/{order}', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::delete('/orders/delete/{order}', [OrderController::class, 'destroy'])->name('orders.delete');
    

    // DUES
    Route::get('due/orders/', [DueOrderController::class, 'index'])->name('due.index');
    Route::get('due/order/view/{order}', [DueOrderController::class, 'show'])->name('due.show');
    Route::get('due/order/edit/{order}', [DueOrderController::class, 'edit'])->name('due.edit');
    Route::put('due/order/update/{order}', [DueOrderController::class, 'update'])->name('due.update');

    // TODO: Remove from OrderController
    Route::get('/orders/details/{order_id}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');


    // Route Purchases
    Route::get('/purchases/approved', [PurchaseController::class, 'approvedPurchases'])->name('purchases.approvedPurchases');
    Route::get('/purchases/report', [PurchaseController::class, 'purchaseReport'])->name('purchases.purchaseReport');
    Route::get('/purchases/report/export', [PurchaseController::class, 'getPurchaseReport'])->name('purchases.getPurchaseReport');
    Route::post('/purchases/report/export', [PurchaseController::class, 'exportPurchaseReport'])->name('purchases.exportPurchaseReport');

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    //Route::get('/purchases/show/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

    //Route::get('/purchases/edit/{purchase}', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::post('/purchases/update/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/delete/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.delete');

    // Route Quotations
    // Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::post('/quotations/complete/{quotation}', [QuotationController::class, 'update'])->name('quotations.complete');
    Route::delete('/quotations/delete/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.delete');

    // //API derived infographs
    Route::get('/api/get-monthly-revenue-data', [OrderController::class, 'getMonthlyRevenueData'])
    ->name('orders.monthlyRevenueData');

    Route::get('/api/get-products-available-data', [ProductController::class, 'getProductsAvailableData'])
    ->name('products.productsAvailableData');

    Route::get('/api/get-top-sold-products-data', [OrderController::class, 'getTopSoldProductsData'])
    ->name('orders.topSoldProductsData');

    Route::get('/api/get-sold-by-type-data', [OrderController::class, 'getSoldByTypeData'])
    ->name('orders.soldByTypeData');

    // Test
    Route::get('/test-email', [TestController::class, 'sendTestEmail']);
});

require __DIR__.'/auth.php';

Route::get('test/', function (){
    return view('test');
});
