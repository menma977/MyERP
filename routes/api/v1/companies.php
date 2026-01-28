<?php

use App\Http\Controllers\Companies\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->name('company.')->middleware(['auth:sanctum', 'can:company.index'])->group(function () {
    Route::get('index', [CompanyController::class, 'index'])->name('index');
    Route::get('show/{id}', [CompanyController::class, 'show'])->name('show')->middleware('can:company.show');
    Route::post('store', [CompanyController::class, 'store'])->name('store')->middleware('can:company.store');
    Route::put('update/{id}', [CompanyController::class, 'update'])->name('update')->middleware('can:company.update');
    Route::delete('delete/{id}', [CompanyController::class, 'delete'])->name('delete')->middleware('can:company.delete');
    Route::post('restore/{id}', [CompanyController::class, 'restore'])->name('restore')->middleware('can:company.restore');
    Route::delete('destroy/{id}', [CompanyController::class, 'destroy'])->name('destroy')->middleware('can:company.destroy');
});
