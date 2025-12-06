<?php

use App\Http\Controllers\Purchases\PurchaseOrderComponentController;
use App\Http\Controllers\Purchases\PurchaseOrderController;
use App\Http\Controllers\Purchases\PurchaseProcurementComponentController;
use App\Http\Controllers\Purchases\PurchaseProcurementController;
use App\Http\Controllers\Purchases\PurchaseRequestComponentController;
use App\Http\Controllers\Purchases\PurchaseRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('purchase')->name('purchase.')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('request')->name('request.')->middleware('can:purchase.request.index')->group(function () {
        Route::get('index', [PurchaseRequestController::class, 'index'])->name('index');
        Route::get('show/{id}', [PurchaseRequestController::class, 'show'])->name('show')->middleware('can:purchase.request.show');
        Route::post('store', [PurchaseRequestController::class, 'store'])->name('store')->middleware('can:purchase.request.store');
        Route::put('update/{id}', [PurchaseRequestController::class, 'update'])->name('update')->middleware('can:purchase.request.update');
        Route::delete('delete/{id}', [PurchaseRequestController::class, 'delete'])->name('delete')->middleware('can:purchase.request.delete');
        Route::post('restore/{id}', [PurchaseRequestController::class, 'restore'])->name('restore')->middleware('can:purchase.request.restore');
        Route::delete('destroy/{id}', [PurchaseRequestController::class, 'destroy'])->name('destroy')->middleware('can:purchase.request.destroy');
        Route::post('approve/{id}', [PurchaseRequestController::class, 'approve'])->name('approve')->middleware('can:purchase.request.store');
        Route::post('reject/{id}', [PurchaseRequestController::class, 'reject'])->name('reject')->middleware('can:purchase.request.store');
        Route::post('cancel/{id}', [PurchaseRequestController::class, 'cancel'])->name('cancel')->middleware('can:purchase.request.store');
        Route::post('rollback/{id}', [PurchaseRequestController::class, 'rollback'])->name('rollback')->middleware('can:purchase.request.store');
        Route::post('force/{id}', [PurchaseRequestController::class, 'force'])->name('force')->middleware('can:purchase.request.store');

        Route::prefix('component/{purchase_request_id}')->name('component.')->middleware('can:purchase.component.index')->group(function () {
            Route::get('index', [PurchaseRequestComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [PurchaseRequestComponentController::class, 'show'])->name('show')->middleware('can:purchase.component.show');
            Route::post('store', [PurchaseRequestComponentController::class, 'store'])->name('store')->middleware('can:purchase.component.store');
            Route::put('update/{id}', [PurchaseRequestComponentController::class, 'update'])->name('update')->middleware('can:purchase.component.update');
            Route::delete('delete/{id}', [PurchaseRequestComponentController::class, 'delete'])->name('delete')->middleware('can:purchase.component.delete');
            Route::post('restore/{id}', [PurchaseRequestComponentController::class, 'restore'])->name('restore')->middleware('can:purchase.component.restore');
            Route::delete('destroy/{id}', [PurchaseRequestComponentController::class, 'destroy'])->name('destroy')->middleware('can:purchase.component.destroy');
        });
    });

    Route::prefix('procurement')->name('procurement.')->middleware('can:purchase.procurement.index')->group(function () {
        Route::get('index', [PurchaseProcurementController::class, 'index'])->name('index');
        Route::get('show/{id}', [PurchaseProcurementController::class, 'show'])->name('show')->middleware('can:purchase.procurement.show');
        Route::put('update/{id}', [PurchaseProcurementController::class, 'update'])->name('update')->middleware('can:purchase.procurement.update');
        Route::post('restore/{id}', [PurchaseProcurementController::class, 'restore'])->name('restore')->middleware('can:purchase.procurement.restore');
        Route::delete('destroy/{id}', [PurchaseProcurementController::class, 'destroy'])->name('destroy')->middleware('can:purchase.procurement.destroy');
        Route::post('approve/{id}', [PurchaseProcurementController::class, 'approve'])->name('approve')->middleware('can:purchase.procurement.store');
        Route::post('reject/{id}', [PurchaseProcurementController::class, 'reject'])->name('reject')->middleware('can:purchase.procurement.store');
        Route::post('cancel/{id}', [PurchaseProcurementController::class, 'cancel'])->name('cancel')->middleware('can:purchase.procurement.store');
        Route::post('rollback/{id}', [PurchaseProcurementController::class, 'rollback'])->name('rollback')->middleware('can:purchase.procurement.store');
        Route::post('force/{id}', [PurchaseProcurementController::class, 'force'])->name('force')->middleware('can:purchase.procurement.store');

        Route::prefix('component/{purchase_procurement_id}')->name('component.')->middleware('can:purchase.component.index')->group(function () {
            Route::get('index', [PurchaseProcurementComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [PurchaseProcurementComponentController::class, 'show'])->name('show')->middleware('can:purchase.component.show');
            Route::post('store', [PurchaseProcurementComponentController::class, 'store'])->name('store')->middleware('can:purchase.component.store');
            Route::put('update/{id}', [PurchaseProcurementComponentController::class, 'update'])->name('update')->middleware('can:purchase.component.update');
            Route::delete('delete/{id}', [PurchaseProcurementComponentController::class, 'delete'])->name('delete')->middleware('can:purchase.component.delete');
            Route::post('restore/{id}', [PurchaseProcurementComponentController::class, 'restore'])->name('restore')->middleware('can:purchase.component.restore');
            Route::delete('destroy/{id}', [PurchaseProcurementComponentController::class, 'destroy'])->name('destroy')->middleware('can:purchase.component.destroy');
        });
    });

    Route::prefix('order')->name('order.')->middleware('can:purchase.order.index')->group(function () {
        Route::get('index', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('show/{id}', [PurchaseOrderController::class, 'show'])->name('show')->middleware('can:purchase.order.show');
        Route::put('update/{id}', [PurchaseOrderController::class, 'update'])->name('update')->middleware('can:purchase.order.update');
        Route::delete('delete/{id}', [PurchaseOrderController::class, 'delete'])->name('delete')->middleware('can:purchase.order.delete');
        Route::post('restore/{id}', [PurchaseOrderController::class, 'restore'])->name('restore')->middleware('can:purchase.order.restore');
        Route::delete('destroy/{id}', [PurchaseOrderController::class, 'destroy'])->name('destroy')->middleware('can:purchase.order.destroy');
        Route::post('approve/{id}', [PurchaseOrderController::class, 'approve'])->name('approve')->middleware('can:purchase.order.store');
        Route::post('reject/{id}', [PurchaseOrderController::class, 'reject'])->name('reject')->middleware('can:purchase.order.store');
        Route::post('cancel/{id}', [PurchaseOrderController::class, 'cancel'])->name('cancel')->middleware('can:purchase.order.store');
        Route::post('rollback/{id}', [PurchaseOrderController::class, 'rollback'])->name('rollback')->middleware('can:purchase.order.store');
        Route::post('force/{id}', [PurchaseOrderController::class, 'force'])->name('force')->middleware('can:purchase.order.store');

        Route::prefix('component/{purchase_order_id}')->name('component.')->middleware('can:purchase.order.component.index')->group(function () {
            Route::get('index', [PurchaseOrderComponentController::class, 'index'])->name('index');
            Route::get('show/{id}', [PurchaseOrderComponentController::class, 'show'])->name('show')->middleware('can:purchase.order.component.show');
            Route::post('store', [PurchaseOrderComponentController::class, 'store'])->name('store')->middleware('can:purchase.order.component.store');
            Route::put('update/{id}', [PurchaseOrderComponentController::class, 'update'])->name('update')->middleware('can:purchase.order.component.update');
            Route::delete('delete/{id}', [PurchaseOrderComponentController::class, 'delete'])->name('delete')->middleware('can:purchase.order.component.delete');
            Route::post('restore/{id}', [PurchaseOrderComponentController::class, 'restore'])->name('restore')->middleware('can:purchase.order.component.restore');
            Route::delete('destroy/{id}', [PurchaseOrderComponentController::class, 'destroy'])->name('destroy')->middleware('can:purchase.order.component.destroy');
        });
    });
});
