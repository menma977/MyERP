<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CreatedByTrait
{
	/**
	 * @return BelongsTo<User, $this>
	 */
	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
