<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UpdatedByObserver
{
	/**
	 * Handle the "updating" event for the given model.
	 *
	 * @param Model $model The model instance being updated.
	 */
	public function updating(Model $model): void
	{
		if (property_exists($model, 'updated_by') && !$model->updated_by) {
			$model->updated_by = Auth::id();
		}
	}

	/**
	 * Handle the "updated" event for the model.
	 *
	 * @param Model $model The model being updated.
	 */
	public function updated(Model $model): void
	{
		if (property_exists($model, 'updated_by') && !$model->updated_by) {
			$model->updated_by = Auth::id();
			$model->saveQuietly();
		}
	}
}
