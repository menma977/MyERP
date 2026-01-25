<?php

use App\Http\Controllers\Vendors\VendorAccountPayableComponentController;
use App\Http\Controllers\Vendors\VendorAccountPayableController;
use App\Http\Controllers\Vendors\VendorComponentController;
use App\Http\Controllers\Vendors\VendorController;
use App\Http\Controllers\Vendors\VendorInvoiceComponentController;
use App\Http\Controllers\Vendors\VendorInvoiceController;
use App\Http\Controllers\Vendors\VendorPaymentComponentController;
use App\Http\Controllers\Vendors\VendorPaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('vendor')->name('vendor.')->middleware(['auth:sanctum', 'can:vendor.index'])->group(function () {
    Route::get('index', [VendorController::class, 'index'])->name('index');
    Route::get('show/{id}', [VendorController::class, 'show'])->name('show')->middleware('can:vendor.show');
    Route::post('store', [VendorController::class, 'store'])->name('store')->middleware('can:vendor.store');
    Route::put('update/{id}', [VendorController::class, 'update'])->name('update')->middleware('can:vendor.update');
    Route::delete('delete/{id}', [VendorController::class, 'delete'])->name('delete')->middleware('can:vendor.delete');

    Route::prefix('component/{vendor_id}')->name('component.')->middleware('can:vendor.component.index')->group(function () {
        Route::get('index', [VendorComponentController::class, 'index'])->name('index');
        Route::get('show/{id}', [VendorComponentController::class, 'show'])->name('show')->middleware('can:vendor.component.show');
        Route::post('store', [VendorComponentController::class, 'store'])->name('store')->middleware('can:vendor.component.store');
        Route::put('update/{id}', [VendorComponentController::class, 'update'])->name('update')->middleware('can:vendor.component.update');
        Route::delete('delete/{id}', [VendorComponentController::class, 'delete'])->name('delete')->middleware('can:vendor.component.delete');
        Route::post('restore/{id}', [VendorComponentController::class, 'restore'])->name('restore')->middleware('can:vendor.component.restore');
        Route::delete('destroy/{id}', [VendorComponentController::class, 'destroy'])->name('destroy')->middleware('can:vendor.component.destroy');
    });

    Route::prefix('invoice')->name('invoice.')->middleware('can:vendor.invoice.index')->group(function () {
        Route::get('index', [VendorInvoiceController::class, 'index'])->name('index');
        Route::get('show/{id}', [VendorInvoiceController::class, 'show'])->name('show')->middleware('can:vendor.invoice.show');
        Route::post('store', [VendorInvoiceController::class, 'store'])->name('store')->middleware('can:vendor.invoice.store');
        Route::put('update/{id}', [VendorInvoiceController::class, 'update'])->name('update')->middleware('can:vendor.invoice.update');
        Route::delete('delete/{id}', [VendorInvoiceController::class, 'delete'])->name('delete')->middleware('can:vendor.invoice.delete');
        Route::post('restore/{id}', [VendorInvoiceController::class, 'restore'])->name('restore')->middleware('can:vendor.invoice.restore');
        Route::delete('destroy/{id}', [VendorInvoiceController::class, 'destroy'])->name('destroy')->middleware('can:vendor.invoice.destroy');
        Route::post('approve/{id}', [VendorInvoiceController::class, 'approve'])->name('approve')->middleware('can:vendor.invoice.approve');
        Route::post('reject/{id}', [VendorInvoiceController::class, 'reject'])->name('reject')->middleware('can:vendor.invoice.reject');
        Route::post('cancel/{id}', [VendorInvoiceController::class, 'cancel'])->name('cancel')->middleware('can:vendor.invoice.cancel');
        Route::post('rollback/{id}', [VendorInvoiceController::class, 'rollback'])->name('rollback')->middleware('can:vendor.invoice.rollback');
        Route::post('force/{id}', [VendorInvoiceController::class, 'force'])->name('force')->middleware('can:vendor.invoice.force');

        Route::prefix('component/{vendor_invoice_id}')->name('component.')->middleware('can:vendor.invoice.component.index')->group(function () {
            Route::get('index', [VendorInvoiceComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [VendorInvoiceComponentController::class, 'show'])->name('show')->middleware('can:vendor.invoice.component.show');
            Route::post('store', [VendorInvoiceComponentController::class, 'store'])->name('store')->middleware('can:vendor.invoice.component.store');
            Route::put('update/{id}', [VendorInvoiceComponentController::class, 'update'])->name('update')->middleware('can:vendor.invoice.component.update');
            Route::delete('delete/{id}', [VendorInvoiceComponentController::class, 'delete'])->name('delete')->middleware('can:vendor.invoice.component.delete');
            Route::post('restore/{id}', [VendorInvoiceComponentController::class, 'restore'])->name('restore')->middleware('can:vendor.invoice.component.restore');
            Route::delete('destroy/{id}', [VendorInvoiceComponentController::class, 'destroy'])->name('destroy')->middleware('can:vendor.invoice.component.destroy');
        });
    });

    Route::prefix('account.payable')->name('account.payable.')->middleware('can:vendor.account.payable.index')->group(function () {
        Route::get('index', [VendorAccountPayableController::class, 'index'])->name('index');
        Route::get('show/{id}', [VendorAccountPayableController::class, 'show'])->name('show')->middleware('can:vendor.account.payable.show');
        Route::post('store', [VendorAccountPayableController::class, 'store'])->name('store')->middleware('can:vendor.account.payable.store');
        Route::put('update/{id}', [VendorAccountPayableController::class, 'update'])->name('update')->middleware('can:vendor.account.payable.update');
        Route::delete('delete/{id}', [VendorAccountPayableController::class, 'delete'])->name('delete')->middleware('can:vendor.account.payable.delete');
        Route::post('restore/{id}', [VendorAccountPayableController::class, 'restore'])->name('restore')->middleware('can:vendor.account.payable.restore');
        Route::delete('destroy/{id}', [VendorAccountPayableController::class, 'destroy'])->name('destroy')->middleware('can:vendor.account.payable.destroy');
        Route::post('approve/{id}', [VendorAccountPayableController::class, 'approve'])->name('approve')->middleware('can:vendor.account.payable.approve');
        Route::post('reject/{id}', [VendorAccountPayableController::class, 'reject'])->name('reject')->middleware('can:vendor.account.payable.reject');
        Route::post('cancel/{id}', [VendorAccountPayableController::class, 'cancel'])->name('cancel')->middleware('can:vendor.account.payable.cancel');
        Route::post('rollback/{id}', [VendorAccountPayableController::class, 'rollback'])->name('rollback')->middleware('can:vendor.account.payable.rollback');
        Route::post('force/{id}', [VendorAccountPayableController::class, 'force'])->name('force')->middleware('can:vendor.account.payable.force');

        Route::prefix('component/{vendor_account_payable_id}')->name('component.')->middleware('can:vendor.account.payable.component.index')->group(function () {
            Route::get('index', [VendorAccountPayableComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [VendorAccountPayableComponentController::class, 'show'])->name('show')->middleware('can:vendor.account.payable.component.show');
            Route::post('store', [VendorAccountPayableComponentController::class, 'store'])->name('store')->middleware('can:vendor.account.payable.component.store');
            Route::put('update/{id}', [VendorAccountPayableComponentController::class, 'update'])->name('update')->middleware('can:vendor.account.payable.component.update');
            Route::delete('delete/{id}', [VendorAccountPayableComponentController::class, 'delete'])->name('delete')->middleware('can:vendor.account.payable.component.delete');
            Route::post('restore/{id}', [VendorAccountPayableComponentController::class, 'restore'])->name('restore')->middleware('can:vendor.account.payable.component.restore');
            Route::delete('destroy/{id}', [VendorAccountPayableComponentController::class, 'destroy'])->name('destroy')->middleware('can:vendor.account.payable.component.destroy');
        });
    });

    Route::prefix('payment')->name('payment.')->middleware('can:vendor.payment.index')->group(function () {
        Route::get('index', [VendorPaymentController::class, 'index'])->name('index');
        Route::get('show/{id}', [VendorPaymentController::class, 'show'])->name('show')->middleware('can:vendor.payment.show');
        Route::post('store', [VendorPaymentController::class, 'store'])->name('store')->middleware('can:vendor.payment.store');
        Route::put('update/{id}', [VendorPaymentController::class, 'update'])->name('update')->middleware('can:vendor.payment.update');
        Route::delete('delete/{id}', [VendorPaymentController::class, 'delete'])->name('delete')->middleware('can:vendor.payment.delete');
        Route::post('restore/{id}', [VendorPaymentController::class, 'restore'])->name('restore')->middleware('can:vendor.payment.restore');
        Route::delete('destroy/{id}', [VendorPaymentController::class, 'destroy'])->name('destroy')->middleware('can:vendor.payment.destroy');
        Route::post('approve/{id}', [VendorPaymentController::class, 'approve'])->name('approve')->middleware('can:vendor.payment.approve');
        Route::post('reject/{id}', [VendorPaymentController::class, 'reject'])->name('reject')->middleware('can:vendor.payment.reject');
        Route::post('cancel/{id}', [VendorPaymentController::class, 'cancel'])->name('cancel')->middleware('can:vendor.payment.cancel');
        Route::post('rollback/{id}', [VendorPaymentController::class, 'rollback'])->name('rollback')->middleware('can:vendor.payment.rollback');
        Route::post('force/{id}', [VendorPaymentController::class, 'force'])->name('force')->middleware('can:vendor.payment.force');

        Route::prefix('component/{vendor_payment_id}')->name('component.')->middleware('can:vendor.payment.component.index')->group(function () {
            Route::get('index', [VendorPaymentComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [VendorPaymentComponentController::class, 'show'])->name('show')->middleware('can:vendor.payment.component.show');
            Route::post('store', [VendorPaymentComponentController::class, 'store'])->name('store')->middleware('can:vendor.payment.component.store');
            Route::put('update/{id}', [VendorPaymentComponentController::class, 'update'])->name('update')->middleware('can:vendor.payment.component.update');
            Route::delete('delete/{id}', [VendorPaymentComponentController::class, 'delete'])->name('delete')->middleware('can:vendor.payment.component.delete');
            Route::post('restore/{id}', [VendorPaymentComponentController::class, 'restore'])->name('restore')->middleware('can:vendor.payment.component.restore');
            Route::delete('destroy/{id}', [VendorPaymentComponentController::class, 'destroy'])->name('destroy')->middleware('can:vendor.payment.component.destroy');
        });
    });
});
