<?php

use App\Http\Controllers\Items\BatchController;
use App\Http\Controllers\Items\ItemController;
use App\Http\Controllers\Items\StockController;
use App\Http\Controllers\Items\StockHistoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('item')->name('item.')->middleware('can:item.index')->group(function () {
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
    });
});
