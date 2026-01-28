<?php

use App\Http\Controllers\Transactions\ExpenseController;
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

Route::prefix('Expense')->name('Expense.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('index', [ExpenseController::class, 'index'])->name('index')->middleware('can:transaction.Expense.index');
    Route::post('store', [ExpenseController::class, 'store'])->name('store')->middleware('can:transaction.Expense.store');
    Route::get('show/{id}', [ExpenseController::class, 'show'])->name('show')->middleware('can:transaction.Expense.show');
    Route::put('update/{id}', [ExpenseController::class, 'update'])->name('update')->middleware('can:transaction.Expense.update');
    Route::delete('delete/{id}', [ExpenseController::class, 'delete'])->name('delete')->middleware('can:transaction.Expense.delete');
    Route::post('restore/{id}', [ExpenseController::class, 'restore'])->name('restore')->middleware('can:transaction.Expense.restore');
    Route::delete('destroy/{id}', [ExpenseController::class, 'destroy'])->name('destroy')->middleware('can:transaction.Expense.destroy');
    Route::post('approve/{id}', [ExpenseController::class, 'approve'])->name('approve')->middleware('can:transaction.Expense.approve');
    Route::post('reject/{id}', [ExpenseController::class, 'reject'])->name('reject')->middleware('can:transaction.Expense.reject');
    Route::post('cancel/{id}', [ExpenseController::class, 'cancel'])->name('cancel')->middleware('can:transaction.Expense.cancel');
    Route::post('rollback/{id}', [ExpenseController::class, 'rollback'])->name('rollback')->middleware('can:transaction.Expense.rollback');
    Route::post('force/{id}', [ExpenseController::class, 'force'])->name('force')->middleware('can:transaction.Expense.force');
});
