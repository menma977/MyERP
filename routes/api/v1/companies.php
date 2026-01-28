<?php

use App\Http\Controllers\Companies\CompanyController;
use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->name('company.')->middleware(['auth:sanctum'])->group(function () {
    Route::middleware('can:company.index')->group(function () {
        Route::get('index', [CompanyController::class, 'index'])->name('index');
        Route::get('show/{id}', [CompanyController::class, 'show'])->name('show')->middleware('can:company.show');
        Route::post('store', [CompanyController::class, 'store'])->name('store')->middleware('can:company.store');
        Route::put('update/{id}', [CompanyController::class, 'update'])->name('update')->middleware('can:company.update');
        Route::delete('delete/{id}', [CompanyController::class, 'delete'])->name('delete')->middleware('can:company.delete');
        Route::post('restore/{id}', [CompanyController::class, 'restore'])->name('restore')->middleware('can:company.restore');
        Route::delete('destroy/{id}', [CompanyController::class, 'destroy'])->name('destroy')->middleware('can:company.destroy');
    });

    Route::prefix('customer')->name('customer.')->middleware('can:company.customer.index')->group(function () {
        Route::get('index', [CustomerController::class, 'index'])->name('index');
        Route::get('show/{id}', [CustomerController::class, 'show'])->name('show')->middleware('can:company.customer.show');
        Route::post('store', [CustomerController::class, 'store'])->name('store')->middleware('can:company.customer.store');
        Route::put('update/{id}', [CustomerController::class, 'update'])->name('update')->middleware('can:company.customer.update');
        Route::delete('delete/{id}', [CustomerController::class, 'delete'])->name('delete')->middleware('can:company.customer.delete');
        Route::post('restore/{id}', [CustomerController::class, 'restore'])->name('restore')->middleware('can:company.customer.restore');
        Route::delete('destroy/{id}', [CustomerController::class, 'destroy'])->name('destroy')->middleware('can:company.customer.destroy');
    });
});
