<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatedByObserver
{
    /**
     * Handle the "creating" event of the model.
     *
     * @param  Model  $model  The model instance being created.
     */
    public function creating(Model $model): void
    {
        if (property_exists($model, 'created_by') && ! $model->created_by) {
            $model->created_by = Auth::id();
        }

        if (property_exists($model, 'company_id') && ! $model->company_id) {
            $model->company_id = $this->resolveCompanyId();
        }
    }

    /**
     * Handle the "created" event of the model.
     *
     * Sets the `created_by` attribute to the authenticated user's ID if not already set,
     * and saves the model silently without firing further events.
     *
     * @param  Model  $model  The model instance after being created.
     */
    public function created(Model $model): void
    {
        if (property_exists($model, 'created_by') && ! $model->created_by) {
            $model->created_by = Auth::id();
            $model->saveQuietly();
        }

        if (property_exists($model, 'company_id') && ! $model->company_id) {
            $model->company_id = $this->resolveCompanyId();
            $model->saveQuietly();
        }
    }

    private function resolveCompanyId(): ?int
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        return $user->currentAccessToken()?->company_id;
    }
}
