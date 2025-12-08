<?php

use App\Http\Controllers\Items\BatchController;
use App\Http\Controllers\Items\GoodReceiptComponentController;
use App\Http\Controllers\Items\GoodReceiptController;
use App\Http\Controllers\Items\ItemController;
use App\Http\Controllers\Items\StockController;
use App\Http\Controllers\Items\StockHistoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('item')->name('item.')->middleware(['auth:sanctum', 'can:item.index'])->group(function () {
    Route::get('index', [ItemController::class, 'index'])->name('index');
    Route::get('show/{id}', [ItemController::class, 'show'])->name('show')->middleware('can:item.show');
    Route::post('store', [ItemController::class, 'store'])->name('store')->middleware('can:item.store');
    Route::put('update/{id}', [ItemController::class, 'update'])->name('update')->middleware('can:item.update');
    Route::delete('delete/{id}', [ItemController::class, 'delete'])->name('delete')->middleware('can:item.delete');
    Route::delete('destroy/{id}', [ItemController::class, 'destroy'])->name('destroy')->middleware('can:item.destroy');
    Route::post('restore/{id}', [ItemController::class, 'restore'])->name('restore')->middleware('can:item.restore');

    Route::prefix('batch')->name('batch.')->middleware('can:item.batch.index')->group(function () {
        Route::get('index', [BatchController::class, 'index'])->name('index');
        Route::get('show/{id}', [BatchController::class, 'show'])->name('show')->middleware('can:item.batch.show');
    });

    Route::prefix('stock')->name('stock.')->middleware('can:item.batch.stock.index')->group(function () {
        Route::get('index', [StockController::class, 'index'])->name('index');
        Route::get('show/{id}', [StockController::class, 'show'])->name('show')->middleware('can:item.batch.stock.show');
    });

    Route::prefix('stock-history')->name('stock-history.')->middleware('can:item.batch.stock.history.index')->group(function () {
        Route::get('index', [StockHistoryController::class, 'index'])->name('index');
        Route::get('show/{id}', [StockHistoryController::class, 'show'])->name('show')->middleware('can:item.batch.stock.history.show');
    });

    Route::prefix('good')->name('good.')->group(function () {
        Route::prefix('receipt')->name('receipt.')->middleware('can:good.receipt.index')->group(function () {
            Route::get('index', [GoodReceiptController::class, 'index'])->name('index');
            Route::get('show/{id}', [GoodReceiptController::class, 'show'])->name('show')->middleware('can:good.receipt.show');
            Route::post('store', [GoodReceiptController::class, 'store'])->name('store')->middleware('can:good.receipt.store');
            Route::put('update/{id}', [GoodReceiptController::class, 'update'])->name('update')->middleware('can:good.receipt.update');
            Route::delete('delete/{id}', [GoodReceiptController::class, 'delete'])->name('delete')->middleware('can:good.receipt.delete');
            Route::post('restore/{id}', [GoodReceiptController::class, 'restore'])->name('restore')->middleware('can:good.receipt.restore');
            Route::delete('destroy/{id}', [GoodReceiptController::class, 'destroy'])->name('destroy')->middleware('can:good.receipt.destroy');
            Route::post('approve/{id}', [GoodReceiptController::class, 'approve'])->name('approve')->middleware('can:good.receipt.store');
            Route::post('reject/{id}', [GoodReceiptController::class, 'reject'])->name('reject')->middleware('can:good.receipt.store');
            Route::post('cancel/{id}', [GoodReceiptController::class, 'cancel'])->name('cancel')->middleware('can:good.receipt.store');
            Route::post('rollback/{id}', [GoodReceiptController::class, 'rollback'])->name('rollback')->middleware('can:good.receipt.store');
            Route::post('force/{id}', [GoodReceiptController::class, 'force'])->name('force')->middleware('can:good.receipt.store');

            Route::prefix('component/{good_receipt_id}')->name('component.')->middleware('can:good.receipt.component.index')->group(function () {
                Route::get('index', [GoodReceiptComponentController::class, 'index'])->name('index');
                Route::get('show/{id}', [GoodReceiptComponentController::class, 'show'])->name('show')->middleware('can:good.receipt.component.show');
                Route::post('store', [GoodReceiptComponentController::class, 'store'])->name('store')->middleware('can:good.receipt.component.store');
                Route::put('update/{id}', [GoodReceiptComponentController::class, 'update'])->name('update')->middleware('can:good.receipt.component.update');
                Route::delete('delete/{id}', [GoodReceiptComponentController::class, 'delete'])->name('delete')->middleware('can:good.receipt.component.delete');
                Route::post('restore/{id}', [GoodReceiptComponentController::class, 'restore'])->name('restore')->middleware('can:good.receipt.component.restore');
                Route::delete('destroy/{id}', [GoodReceiptComponentController::class, 'destroy'])->name('destroy')->middleware('can:good.receipt.component.destroy');
            });
        });
    });
});
