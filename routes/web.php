<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/categories', \App\Livewire\Admin\CategoryManager::class)->name('admin.categories');
    Route::get('admin/products', \App\Livewire\Admin\ProductManager::class)->name('admin.products');
    Route::get('admin/suppliers', \App\Livewire\Admin\SupplierManager::class)->name('admin.suppliers');
    Route::get('admin/purchases', \App\Livewire\Admin\PurchaseManager::class)->name('admin.purchases');
    Route::get('admin/pos', \App\Livewire\Admin\PosTerminal::class)->name('admin.pos');
    Route::get('admin/reports/daily', \App\Livewire\Admin\DailyReportManager::class)->name('admin.reports.daily');
});

require __DIR__.'/settings.php';
