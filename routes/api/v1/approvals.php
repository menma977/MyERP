<?php

use App\Http\Controllers\Approvals\ApprovalComponentContributorController;
use App\Http\Controllers\Approvals\ApprovalComponentController;
use App\Http\Controllers\Approvals\ApprovalController;
use App\Http\Controllers\Approvals\DictionaryController;
use App\Http\Controllers\Approvals\FlowComponentController;
use App\Http\Controllers\Approvals\FlowController;
use App\Http\Controllers\Approvals\GroupContributorController;
use App\Http\Controllers\Approvals\GroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('approval')->name('approval.')->middleware('can:approval.index')->group(function () {
	Route::get('index', [ApprovalController::class, 'index'])->name('index');
	Route::get('show/{id}', [ApprovalController::class, 'show'])->name('show')->middleware('can:approval.show');
	Route::post('store', [ApprovalController::class, 'store'])->name('store')->middleware('can:approval.store');
	Route::put('update/{id}', [ApprovalController::class, 'update'])->name('update')->middleware('can:approval.update');
	Route::delete('delete/{id}', [ApprovalController::class, 'delete'])->name('delete')->middleware('can:approval.delete');

	Route::prefix('dictionary')->name('dictionary.')->middleware('can:approval.dictionary.index')->group(function () {
		Route::get('index', [DictionaryController::class, 'index'])->name('index');
		Route::get('show/{id}', [DictionaryController::class, 'show'])->name('show')->middleware('can:approval.dictionary.show');
		Route::post('store', [DictionaryController::class, 'store'])->name('store')->middleware('can:approval.dictionary.store');
		Route::put('update/{id}', [DictionaryController::class, 'update'])->name('update')->middleware('can:approval.dictionary.update');
		Route::delete('delete/{id}', [DictionaryController::class, 'delete'])->name('delete')->middleware('can:approval.dictionary.delete');
	});

	Route::prefix('component/{approval_id}')->name('component.')->middleware('can:approval.component.index')->group(function () {
		Route::get('index', [ApprovalComponentController::class, 'index'])->name('index');
		Route::get('show/{id}', [ApprovalComponentController::class, 'show'])->name('show')->middleware('can:approval.component.show');
		Route::post('store', [ApprovalComponentController::class, 'store'])->name('store')->middleware('can:approval.component.store');
		Route::put('update/{id}', [ApprovalComponentController::class, 'update'])->name('update')->middleware('can:approval.component.update');
		Route::delete('delete/{id}', [ApprovalComponentController::class, 'delete'])->name('delete')->middleware('can:approval.component.delete');

		Route::prefix('contributor/{approval_component_id}')->name('contributor.')->middleware('can:approval.component.contributor.index')->group(function () {
			Route::get('index', [ApprovalComponentContributorController::class, 'index'])->name('index');
			Route::get('show/{id}', [ApprovalComponentContributorController::class, 'show'])->name('show')->middleware('can:approval.component.contributor.show');
			Route::post('store', [ApprovalComponentContributorController::class, 'store'])->name('store')->middleware('can:approval.component.contributor.store');
			Route::put('update/{id}', [ApprovalComponentContributorController::class, 'update'])->name('update')->middleware('can:approval.component.contributor.update');
			Route::delete('delete/{id}', [ApprovalComponentContributorController::class, 'delete'])->name('delete')->middleware('can:approval.component.contributor.delete');
		});
	});

	Route::prefix('flow')->name('flow.')->middleware('can:approval.flow.index')->group(function () {
		Route::get('index', [FlowController::class, 'index'])->name('index');
		Route::get('show/{id}', [FlowController::class, 'show'])->name('show')->middleware('can:approval.flow.show');
		Route::post('store', [FlowController::class, 'store'])->name('store')->middleware('can:approval.flow.store');
		Route::put('update/{id}', [FlowController::class, 'update'])->name('update')->middleware('can:approval.flow.update');
		Route::delete('delete/{id}', [FlowController::class, 'delete'])->name('delete')->middleware('can:approval.flow.delete');

		Route::prefix('component/{flow_id}')->name('component.')->middleware('can:approval.flow.component.index')->group(function () {
			Route::get('index', [FlowComponentController::class, 'index'])->name('index');
			Route::get('show/{id}', [FlowComponentController::class, 'show'])->name('show')->middleware('can:approval.flow.component.show');
			Route::post('store', [FlowComponentController::class, 'store'])->name('store')->middleware('can:approval.flow.component.store');
			Route::put('update/{id}', [FlowComponentController::class, 'update'])->name('update')->middleware('can:approval.flow.component.update');
			Route::delete('delete/{id}', [FlowComponentController::class, 'delete'])->name('delete')->middleware('can:approval.flow.component.delete');
		});
	});

	Route::prefix('group')->name('group.')->middleware('can:approval.group.index')->group(function () {
		Route::get('index', [GroupController::class, 'index'])->name('index');
		Route::get('show/{id}', [GroupController::class, 'show'])->name('show')->middleware('can:approval.group.show');
		Route::post('store', [GroupController::class, 'store'])->name('store')->middleware('can:approval.group.store');
		Route::put('update/{id}', [GroupController::class, 'update'])->name('update')->middleware('can:approval.group.update');
		Route::delete('delete/{id}', [GroupController::class, 'delete'])->name('delete')->middleware('can:approval.group.delete');

		Route::prefix('contributor/{group_id}')->name('contributor.')->middleware('can:approval.group.contributor.index')->group(function () {
			Route::get('index', [GroupContributorController::class, 'index'])->name('index');
			Route::get('show/{id}', [GroupContributorController::class, 'show'])->name('show')->middleware('can:approval.group.contributor.show');
			Route::post('store', [GroupContributorController::class, 'store'])->name('store')->middleware('can:approval.group.contributor.store');
			Route::put('update/{id}', [GroupContributorController::class, 'update'])->name('update')->middleware('can:approval.group.contributor.update');
			Route::delete('delete/{id}', [GroupContributorController::class, 'delete'])->name('delete')->middleware('can:approval.group.contributor.delete');
		});
	});
});
