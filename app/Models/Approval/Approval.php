<?php

namespace App\Models\Approval;

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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $approval_flow_id
 * @property string $name
 * @property ApprovalTypeEnum $type The type of workflow (0: parallel or 1: sequential)
 * @property bool $can_change Whether the approval can be changed
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Approval\ApprovalComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read Collection<int, \App\Models\Approval\ApprovalEvent> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Approval\ApprovalFlow $flow
 * @property-read User|null $updatedBy
 * @method static Builder<static>|Approval newModelQuery()
 * @method static Builder<static>|Approval newQuery()
 * @method static Builder<static>|Approval onlyTrashed()
 * @method static Builder<static>|Approval query()
 * @method static Builder<static>|Approval whereApprovalFlowId($value)
 * @method static Builder<static>|Approval whereCanChange($value)
 * @method static Builder<static>|Approval whereCreatedAt($value)
 * @method static Builder<static>|Approval whereCreatedBy($value)
 * @method static Builder<static>|Approval whereDeletedAt($value)
 * @method static Builder<static>|Approval whereDeletedBy($value)
 * @method static Builder<static>|Approval whereId($value)
 * @method static Builder<static>|Approval whereName($value)
 * @method static Builder<static>|Approval whereType($value)
 * @method static Builder<static>|Approval whereUpdatedAt($value)
 * @method static Builder<static>|Approval whereUpdatedBy($value)
 * @method static Builder<static>|Approval withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Approval withoutTrashed()
 * @mixin Eloquent
 */
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class Approval extends Model
{
	use CreatedByTrait, DeletedByTrait, UpdatedByTrait;
	use SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'approval_flow_id',
		'name',
		'type',
		'can_change',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	protected $casts = [
		'type' => ApprovalTypeEnum::class,
		'can_change' => 'boolean',
	];

	/**
	 * Get the approval flow associated with the approval.
	 *
	 * @return BelongsTo<ApprovalFlow, $this>
	 */
	public function flow(): BelongsTo
	{
		return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id', 'id');
	}

	/**
	 * Get the components associated with the approval.
	 *
	 * @return HasMany<ApprovalComponent, $this>
	 */
	public function components(): HasMany
	{
		return $this->hasMany(ApprovalComponent::class);
	}

	/**
	 * Get the events associated with the approval.
	 *
	 * @return HasMany<ApprovalEvent, $this>
	 */
	public function events(): HasMany
	{
		return $this->hasMany(ApprovalEvent::class);
	}
}
