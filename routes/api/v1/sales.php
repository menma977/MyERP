<?php

use App\Http\Controllers\Sales\SalesOrderComponentController;
use App\Http\Controllers\Sales\SalesOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('sales')->name('sales.')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('order')->name('order.')->middleware('can:sales.order.index')->group(function () {
        Route::get('index', [SalesOrderController::class, 'index'])->name('index');
        Route::get('show/{id}', [SalesOrderController::class, 'show'])->name('show')->middleware('can:sales.order.show');
        Route::post('store', [SalesOrderController::class, 'store'])->name('store')->middleware('can:sales.order.store');
        Route::put('update/{id}', [SalesOrderController::class, 'update'])->name('update')->middleware('can:sales.order.update');
        Route::delete('delete/{id}', [SalesOrderController::class, 'delete'])->name('delete')->middleware('can:sales.order.delete');
        Route::post('restore/{id}', [SalesOrderController::class, 'restore'])->name('restore')->middleware('can:sales.order.restore');
        Route::delete('destroy/{id}', [SalesOrderController::class, 'destroy'])->name('destroy')->middleware('can:sales.order.destroy');
        Route::post('approve/{id}', [SalesOrderController::class, 'approve'])->name('approve')->middleware('can:sales.order.store');
        Route::post('reject/{id}', [SalesOrderController::class, 'reject'])->name('reject')->middleware('can:sales.order.store');

        Route::prefix('component/{sales_order_id}')->name('component.')->middleware('can:sales.order.component.index')->group(function () {
            Route::get('index', [SalesOrderComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [SalesOrderComponentController::class, 'show'])->name('show')->middleware('can:sales.order.component.show');
            Route::post('store', [SalesOrderComponentController::class, 'store'])->name('store')->middleware('can:sales.order.component.store');
            Route::put('update/{id}', [SalesOrderComponentController::class, 'update'])->name('update')->middleware('can:sales.order.component.update');
            Route::delete('delete/{id}', [SalesOrderComponentController::class, 'delete'])->name('delete')->middleware('can:sales.order.component.delete');
            Route::post('restore/{id}', [SalesOrderComponentController::class, 'restore'])->name('restore')->middleware('can:sales.order.component.restore');
            Route::delete('destroy/{id}', [SalesOrderComponentController::class, 'destroy'])->name('destroy')->middleware('can:sales.order.component.destroy');
        });
    });
});
