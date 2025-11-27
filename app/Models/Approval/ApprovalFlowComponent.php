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
 * @property string $approval_flow_id
 * @property string $approval_dictionary_id
 * @property string $key
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Approval\ApprovalDictionary|null $dictionary
 * @property-read \App\Models\Approval\ApprovalFlow|null $flow
 * @property-read User|null $updatedBy
 * @method static Builder<static>|ApprovalFlowComponent newModelQuery()
 * @method static Builder<static>|ApprovalFlowComponent newQuery()
 * @method static Builder<static>|ApprovalFlowComponent onlyTrashed()
 * @method static Builder<static>|ApprovalFlowComponent query()
 * @method static Builder<static>|ApprovalFlowComponent whereApprovalDictionaryId($value)
 * @method static Builder<static>|ApprovalFlowComponent whereApprovalFlowId($value)
 * @method static Builder<static>|ApprovalFlowComponent whereCreatedAt($value)
 * @method static Builder<static>|ApprovalFlowComponent whereCreatedBy($value)
 * @method static Builder<static>|ApprovalFlowComponent whereDeletedAt($value)
 * @method static Builder<static>|ApprovalFlowComponent whereDeletedBy($value)
 * @method static Builder<static>|ApprovalFlowComponent whereId($value)
 * @method static Builder<static>|ApprovalFlowComponent whereKey($value)
 * @method static Builder<static>|ApprovalFlowComponent whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalFlowComponent whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalFlowComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalFlowComponent withoutTrashed()
 * @mixin Eloquent
 */
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class ApprovalFlowComponent extends Model
{
	use CreatedByTrait, DeletedByTrait, UpdatedByTrait;
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'approval_flow_id',
		'approval_dictionary_id',
		'key',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	/**
	 * Get the flow associated with this component.
	 *
	 * @return BelongsTo<ApprovalFlow, $this>
	 */
	public function flow(): BelongsTo
	{
		return $this->belongsTo(ApprovalFlow::class);
	}

	/**
	 * Get the dictionary associated with this component.
	 *
	 * @return BelongsTo<ApprovalDictionary, $this>
	 */
	public function dictionary(): BelongsTo
	{
		return $this->belongsTo(ApprovalDictionary::class);
	}
}
