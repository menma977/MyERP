<?php

namespace App\Models\Approval;

use App\Enums\ApprovalStatusEnum;
use App\Enums\ApprovalTypeEnum;
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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $id
 * @property int|null $approval_id
 * @property int $step The step using binary system: 0, 1, 3, 7, etc.
 * @property int $target The target of a binary system: 1, 2, 4, 8, etc.
 * @property string $requestable_type
 * @property string $requestable_id
 * @property ApprovalTypeEnum $type The type of workflow (0: parallel or 1: sequential)
 * @property ApprovalStatusEnum $status The current status of this approval Draft -> Pending -> Approved -> Rejected
 * @property Carbon|null $approved_at
 * @property Carbon|null $rejected_at
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $rollback_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Models\Approval\Approval|null $approval
 * @property-read mixed $can_approve
 * @property-read mixed $component
 * @property-read Collection<int, \App\Models\Approval\ApprovalEventComponent> $components
 * @property-read int|null $components_count
 * @property-read Collection<int, \App\Models\Approval\ApprovalEventContributor> $contributors
 * @property-read int|null $contributors_count
 * @property-read User|null $createdBy
 * @property-read mixed $current_component
 * @property-read User|null $deletedBy
 * @property-read mixed $is_approved
 * @property-read mixed $is_cancelled
 * @property-read mixed $is_rejected
 * @property-read mixed $is_rollback
 * @property-read \Illuminate\Database\Eloquent\Model $requestable
 * @property-read User|null $updatedBy
 * @method static Builder<static>|ApprovalEvent newModelQuery()
 * @method static Builder<static>|ApprovalEvent newQuery()
 * @method static Builder<static>|ApprovalEvent onlyTrashed()
 * @method static Builder<static>|ApprovalEvent query()
 * @method static Builder<static>|ApprovalEvent whereApprovalId($value)
 * @method static Builder<static>|ApprovalEvent whereApprovedAt($value)
 * @method static Builder<static>|ApprovalEvent whereCancelledAt($value)
 * @method static Builder<static>|ApprovalEvent whereCreatedAt($value)
 * @method static Builder<static>|ApprovalEvent whereCreatedBy($value)
 * @method static Builder<static>|ApprovalEvent whereDeletedAt($value)
 * @method static Builder<static>|ApprovalEvent whereDeletedBy($value)
 * @method static Builder<static>|ApprovalEvent whereId($value)
 * @method static Builder<static>|ApprovalEvent whereRejectedAt($value)
 * @method static Builder<static>|ApprovalEvent whereRequestableId($value)
 * @method static Builder<static>|ApprovalEvent whereRequestableType($value)
 * @method static Builder<static>|ApprovalEvent whereRollbackAt($value)
 * @method static Builder<static>|ApprovalEvent whereStatus($value)
 * @method static Builder<static>|ApprovalEvent whereStep($value)
 * @method static Builder<static>|ApprovalEvent whereTarget($value)
 * @method static Builder<static>|ApprovalEvent whereType($value)
 * @method static Builder<static>|ApprovalEvent whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalEvent whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalEvent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalEvent withoutTrashed()
 * @mixin Eloquent
 */
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class ApprovalEvent extends Model
{
	use CreatedByTrait, DeletedByTrait, UpdatedByTrait;
	use HasUlids, SoftDeletes;

	protected $appends = [
		'is_approved',
		'is_rejected',
		'is_cancelled',
		'is_rollback',
		'can_approve',
		'component',
		'current_component',
	];

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'approval_id',
		'step',
		'target',
		'requestable_type',
		'requestable_id',
		'type',
		'status',
		'approved_at',
		'rejected_at',
		'cancelled_at',
		'rollback_at',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	protected $casts = [
		'type' => ApprovalTypeEnum::class,
		'status' => ApprovalStatusEnum::class,
		'approved_at' => 'datetime',
		'rejected_at' => 'datetime',
		'cancelled_at' => 'datetime',
		'rollback_at' => 'datetime',
	];

	/**
	 * Get the approval associated with this event.
	 *
	 * @return BelongsTo<Approval, $this>
	 */
	public function approval(): BelongsTo
	{
		return $this->belongsTo(Approval::class);
	}

	/**
	 * Get the parent requestable model.
	 *
	 * @return MorphTo<Model, $this>
	 */
	public function requestable(): BelongsTo
	{
		return $this->morphTo();
	}

	/**
	 * Get the components associated with this event.
	 *
	 * @return HasMany<ApprovalEventComponent, $this>
	 */
	public function components(): HasMany
	{
		return $this->hasMany(ApprovalEventComponent::class);
	}

	/**
	 * Get the contributors through the components.
	 *
	 * @return HasManyThrough<ApprovalEventContributor, ApprovalEventComponent, $this>
	 */
	public function contributors(): HasManyThrough
	{
		return $this->hasManyThrough(ApprovalEventContributor::class, ApprovalEventComponent::class);
	}

	/**
	 * Get the approval status of the event.
	 *
	 *
	 * @noinspection PhpUnused
	 */
	/**
	 * @return Attribute<bool, never>
	 */
	protected function isApproved(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value, array $attributes) => isset($attributes['approved_at']),
		);
	}

	/**
	 * Get the approval status of the event.
	 *
	 *
	 * @noinspection PhpUnused
	 */
	/**
	 * @return Attribute<bool, never>
	 */
	protected function isRejected(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value, array $attributes) => isset($attributes['rejected_at']),
		);
	}

	/**
	 * Get the approval status of the event.
	 *
	 *
	 * @noinspection PhpUnused
	 */
	/**
	 * @return Attribute<bool, never>
	 */
	protected function isCancelled(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value, array $attributes) => isset($attributes['cancelled_at']),
		);
	}

	/**
	 * Get the approval status of the event.
	 *
	 *
	 * @noinspection PhpUnused
	 */
	/**
	 * @return Attribute<bool, never>
	 */
	protected function isRollback(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value, array $attributes) => isset($attributes['rollback_at']),
		);
	}

	/**
	 * Get the approval status of the event.
	 *
	 *
	 * @noinspection PhpUnused
	 */
	/**
	 * @return Attribute<bool, never>
	 */
	protected function canApprove(): Attribute
	{
		return Attribute::get(function (mixed $value, array $attributes) {
			if ($this->is_approved || $this->is_cancelled || $this->is_rejected) {
				return false;
			}

			if ($this->components()->whereRaw('(step & ?) = 0', [$attributes['step']])->orderBy('step')->count() <= 0) {
				return false;
			}

			$component = $this->components()->whereRaw('(step & ?) = 0', [$attributes['step']])->orderBy('step')->first();
			if ($component && ($component->is_approved || $component->is_cancelled || $component->is_rejected)) {
				return false;
			}

			$contributors = $component->contributors ?? collect();
			if ($contributors->isEmpty()) {
				return true;
			}

			foreach ($contributors as $contributor) {
				if ((int)$contributor->user_id === (int)Auth::id()) {
					return true;
				}
			}

			return false;
		});
	}

	/**
	 * Get the approval status of the event.
	 */
	/**
	 * @return Attribute<ApprovalEventComponent|null, never>
	 */
	protected function component(): Attribute
	{
		return Attribute::get(function (mixed $value, array $attributes) {
			return $this->components()->whereRaw('(step & ?) = 0', [$attributes['step']])->orderBy('step')->first() ?? null;
		});
	}

	/**
	 * Get the approval status of the event.
	 *
	 *
	 * @noinspection PhpUnused
	 */
	/**
	 * @return Attribute<ApprovalEventComponent|null, never>
	 */
	protected function currentComponent(): Attribute
	{
		return Attribute::get(function (mixed $value, array $attributes) {
			return $this->components()->whereRaw('(step & ~?) = 0', [$attributes['step']])->latest('id')->first() ?? null;
		});
	}
}
