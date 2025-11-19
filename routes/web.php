<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public and customer-facing routes
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/t/{code}', [CustomerController::class, 'showTable'])->name('customer.table');
Route::get('/t/{code}/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::post('/order/submit', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/status/{order}', [OrderController::class, 'status'])->name('order.status');
Route::post('/payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Kitchen routes with role gating
Route::middleware(['auth', 'role:kitchen,admin'])->group(function () {
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/kitchen/order/{order}/update-status', [KitchenController::class, 'updateStatus'])->name('kitchen.update');
});

// Cashier routes
Route::middleware(['auth', 'role:cashier,admin'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier/payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('cashier.payments.markPaid');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('menus', AdminMenuController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::get('tables/create', [TableController::class, 'create'])->name('admin.tables.create');
    Route::post('tables', [TableController::class, 'store'])->name('admin.tables.store');
    Route::put('tables/{table}', [TableController::class, 'update'])->name('admin.tables.update');
    Route::get('tables/{table}/edit', [TableController::class, 'edit'])->name('admin.tables.edit');
    Route::post('tables/{table}/regenerate', [TableController::class, 'regenerate'])->name('admin.tables.regenerate');
    Route::get('tables/{table}/download', [TableController::class, 'download'])->name('admin.tables.download');
    Route::delete('tables/{table}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('admin.reports.export.excel');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.export.pdf');
    Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('settings/payment', [SettingController::class, 'editPayment'])->name('admin.settings.payment.edit');
    Route::put('settings/payment', [SettingController::class, 'updatePayment'])->name('admin.settings.payment.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
