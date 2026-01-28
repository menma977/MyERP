<?php

use App\Http\Controllers\Transactions\ExpanseController;
use App\Http\Controllers\Transactions\LedgerComponentController;
use App\Http\Controllers\Transactions\LedgerController;
use App\Http\Controllers\Transactions\PaymentRequestComponentController;
use App\Http\Controllers\Transactions\PaymentRequestController;

Route::prefix('payment')->name('payment.')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('request')->name('request.')->middleware('can:payment.request.index')->group(function () {
        Route::get('index', [PaymentRequestController::class, 'index'])->name('index');
        Route::get('show/{id}', [PaymentRequestController::class, 'show'])->name('show')->middleware('can:payment.request.show');
        Route::put('update/{id}', [PaymentRequestController::class, 'update'])->name('update')->middleware('can:payment.request.update');
        Route::delete('delete/{id}', [PaymentRequestController::class, 'delete'])->name('delete')->middleware('can:payment.request.delete');
        Route::post('restore/{id}', [PaymentRequestController::class, 'restore'])->name('restore')->middleware('can:payment.request.restore');
        Route::delete('destroy/{id}', [PaymentRequestController::class, 'destroy'])->name('destroy')->middleware('can:payment.request.destroy');
        Route::post('approve/{id}', [PaymentRequestController::class, 'approve'])->name('approve')->middleware('can:payment.request.approve');
        Route::post('reject/{id}', [PaymentRequestController::class, 'reject'])->name('reject')->middleware('can:payment.request.reject');
        Route::post('cancel/{id}', [PaymentRequestController::class, 'cancel'])->name('cancel')->middleware('can:payment.request.cancel');
        Route::post('rollback/{id}', [PaymentRequestController::class, 'rollback'])->name('rollback')->middleware('can:payment.request.rollback');
        Route::post('force/{id}', [PaymentRequestController::class, 'force'])->name('force')->middleware('can:payment.request.force');

        Route::prefix('component/{payment_request_id}')->name('component.')->middleware('can:payment.request.component.index')->group(function () {
            Route::get('index', [PaymentRequestComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [PaymentRequestComponentController::class, 'show'])->name('show')->middleware('can:payment.request.component.show');
            Route::post('store', [PaymentRequestComponentController::class, 'store'])->name('store')->middleware('can:payment.request.component.store');
            Route::put('update/{id}', [PaymentRequestComponentController::class, 'update'])->name('update')->middleware('can:payment.request.component.update');
            Route::delete('delete/{id}', [PaymentRequestComponentController::class, 'delete'])->name('delete')->middleware('can:payment.request.component.delete');
            Route::post('restore/{id}', [PaymentRequestComponentController::class, 'restore'])->name('restore')->middleware('can:payment.request.component.restore');
            Route::delete('destroy/{id}', [PaymentRequestComponentController::class, 'destroy'])->name('destroy')->middleware('can:payment.request.component.destroy');
        });
    });
});

Route::prefix('ledger')->name('ledger.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('index', [LedgerController::class, 'index'])->name('index')->middleware('can:ledger.index');
    Route::get('show/{id}', [LedgerController::class, 'show'])->name('show')->middleware('can:ledger.show');

    Route::prefix('component/{ledger_id}')->name('component.')->middleware('can:ledger.component.index')->group(function () {
        Route::get('index', [LedgerComponentController::class, 'index'])->name('index');
        Route::get('show/{id}', [LedgerComponentController::class, 'show'])->name('show')->middleware('can:ledger.component.show');
    });
});

Route::prefix('expanse')->name('expanse.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('index', [ExpanseController::class, 'index'])->name('index')->middleware('can:transaction.expanse.index');
    Route::post('store', [ExpanseController::class, 'store'])->name('store')->middleware('can:transaction.expanse.store');
    Route::get('show/{id}', [ExpanseController::class, 'show'])->name('show')->middleware('can:transaction.expanse.show');
    Route::put('update/{id}', [ExpanseController::class, 'update'])->name('update')->middleware('can:transaction.expanse.update');
    Route::delete('delete/{id}', [ExpanseController::class, 'delete'])->name('delete')->middleware('can:transaction.expanse.delete');
    Route::post('restore/{id}', [ExpanseController::class, 'restore'])->name('restore')->middleware('can:transaction.expanse.restore');
    Route::delete('destroy/{id}', [ExpanseController::class, 'destroy'])->name('destroy')->middleware('can:transaction.expanse.destroy');
    Route::post('approve/{id}', [ExpanseController::class, 'approve'])->name('approve')->middleware('can:transaction.expanse.approve');
    Route::post('reject/{id}', [ExpanseController::class, 'reject'])->name('reject')->middleware('can:transaction.expanse.reject');
    Route::post('cancel/{id}', [ExpanseController::class, 'cancel'])->name('cancel')->middleware('can:transaction.expanse.cancel');
    Route::post('rollback/{id}', [ExpanseController::class, 'rollback'])->name('rollback')->middleware('can:transaction.expanse.rollback');
    Route::post('force/{id}', [ExpanseController::class, 'force'])->name('force')->middleware('can:transaction.expanse.force');
});
