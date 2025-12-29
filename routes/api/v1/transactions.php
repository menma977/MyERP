<?php

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
