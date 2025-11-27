<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatedByObserver
{
	/**
	 * Handle the "creating" event of the model.
	 *
	 * @param Model $model The model instance being created.
	 */
	public function creating(Model $model): void
	{
		if (property_exists($model, 'created_by') && !$model->created_by) {
			$model->created_by = Auth::id();
		}
	}

	/**
	 * Handle the "created" event of the model.
	 *
	 * Sets the `created_by` attribute to the authenticated user's ID if not already set,
	 * and saves the model silently without firing further events.
	 *
	 * @param Model $model The model instance after being created.
	 */
	public function created(Model $model): void
	{
		if (property_exists($model, 'created_by') && !$model->created_by) {
			$model->created_by = Auth::id();
			$model->saveQuietly();
		}
	}
}
