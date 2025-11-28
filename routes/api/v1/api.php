<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [LoginController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [LoginController::class, 'me'])->name('me');
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');
            Route::post('logout/all', [LoginController::class, 'logoutAll'])->name('logout.all');

            Route::prefix('role')->name('role.')->middleware('can:role.index')->group(function () {
                Route::get('index', [RoleController::class, 'index'])->name('index');
                Route::get('show/{id}', [RoleController::class, 'show'])->name('show')->middleware('can:role.show');
                Route::post('store', [RoleController::class, 'store'])->name('store')->middleware('can:role.create');
                Route::put('update/{id}', [RoleController::class, 'update'])->name('update')->middleware('can:role.update');
                Route::delete('delete/{id}', [RoleController::class, 'delete'])->name('delete')->middleware('can:role.delete');
            });

            Route::prefix('permission')->name('permission.')->middleware('can:role.permission.index')->group(function () {
                Route::get('index', [PermissionController::class, 'index'])->name('index');
                Route::get('show/{id}', [PermissionController::class, 'show'])->name('show')->middleware('can:permission.show');
                Route::post('store', [PermissionController::class, 'store'])->name('store')->middleware('can:permission.create');
                Route::put('update/{id}', [PermissionController::class, 'update'])->name('update')->middleware('can:permission.update');
                Route::delete('delete/{id}', [PermissionController::class, 'delete'])->name('delete')->middleware('can:permission.delete');
            });

            Route::prefix('user')->name('user.')->middleware('can:user.index')->group(function () {
                Route::get('index', [UserController::class, 'index'])->name('index');
                Route::get('show/{id}', [UserController::class, 'show'])->name('show')->middleware('can:user.show');
                Route::post('store', [UserController::class, 'store'])->name('store')->middleware('can:user.create');
                Route::put('update/{id}', [UserController::class, 'update'])->name('update')->middleware('can:user.update');
                Route::delete('delete/{id}', [UserController::class, 'delete'])->name('delete')->middleware('can:user.delete');
                Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore')->middleware('can:user.update');
            });
        });
    });
});
