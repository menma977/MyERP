<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DeletedByObserver
{
	/**
	 * Handle the Model "deleted" event.
	 * Ensures deleted_by field is set and saved after model deletion.
	 *
	 * @param Model $model The model that was deleted
	 */
	public function deleted(Model $model): void
	{
		if (property_exists($model, 'deleted_by') && !$model->deleted_by) {
			$model->deleted_by = Auth::id();
			$model->saveQuietly();
		}
	}
}
