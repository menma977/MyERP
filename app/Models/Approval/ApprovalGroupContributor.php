<?php

namespace App\Models\Approval;

use App\Models\User;
use App\Observers\CreatedByObserver;
use App\Observers\DeletedByObserver;
use App\Observers\UpdatedByObserver;
use App\Traits\CreatedByTrait;
use App\Traits\DeletedByTrait;
use App\Traits\UpdatedByTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $approval_group_id
 * @property int $user_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Approval\ApprovalGroup|null $group
 * @property-read User|null $updatedBy
 * @property-read User $user
 * @method static Builder<static>|ApprovalGroupContributor newModelQuery()
 * @method static Builder<static>|ApprovalGroupContributor newQuery()
 * @method static Builder<static>|ApprovalGroupContributor onlyTrashed()
 * @method static Builder<static>|ApprovalGroupContributor query()
 * @method static Builder<static>|ApprovalGroupContributor whereApprovalGroupId($value)
 * @method static Builder<static>|ApprovalGroupContributor whereCreatedAt($value)
 * @method static Builder<static>|ApprovalGroupContributor whereCreatedBy($value)
 * @method static Builder<static>|ApprovalGroupContributor whereDeletedAt($value)
 * @method static Builder<static>|ApprovalGroupContributor whereDeletedBy($value)
 * @method static Builder<static>|ApprovalGroupContributor whereId($value)
 * @method static Builder<static>|ApprovalGroupContributor whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalGroupContributor whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalGroupContributor whereUserId($value)
 * @method static Builder<static>|ApprovalGroupContributor withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalGroupContributor withoutTrashed()
 * @mixin Eloquent
 */
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class ApprovalGroupContributor extends Model
{
	use CreatedByTrait, DeletedByTrait, UpdatedByTrait;
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'approval_group_id',
		'user_id',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	/**
	 * Get the user associated with this group contributor.
	 *
	 * @return BelongsTo<User, $this>
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get the group associated with this contributor.
	 *
	 * @return BelongsTo<ApprovalGroup, $this>
	 */
	public function group(): BelongsTo
	{
		return $this->belongsTo(ApprovalGroup::class);
	}
}
