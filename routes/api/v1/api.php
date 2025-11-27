<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Load domain-specific route files
Route::prefix('v1')->name('v1.')->group(function () {
	Route::prefix('auth')->name('auth.')->group(function () {
		Route::post('login', [LoginController::class, 'login'])->name('login');

		Route::middleware('auth:sanctum')->group(function () {
			Route::get('me', [LoginController::class, 'me'])->name('me');
			Route::post('logout', [LoginController::class, 'logout'])->name('logout');
			Route::post('logout/all', [LoginController::class, 'logoutAll'])->name('logout.all');
		});
	});
});
