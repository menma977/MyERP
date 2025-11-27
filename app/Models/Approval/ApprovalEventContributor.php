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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $approval_event_component_id
 * @property int $user_id
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
 * @property-read ApprovalEventComponent|null $component
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read mixed $is_approved
 * @property-read mixed $is_cancelled
 * @property-read mixed $is_rejected
 * @property-read mixed $is_rollback
 * @property-read User|null $updatedBy
 * @property-read User $user
 *
 * @method static Builder<static>|ApprovalEventContributor newModelQuery()
 * @method static Builder<static>|ApprovalEventContributor newQuery()
 * @method static Builder<static>|ApprovalEventContributor onlyTrashed()
 * @method static Builder<static>|ApprovalEventContributor query()
 * @method static Builder<static>|ApprovalEventContributor whereApprovalEventComponentId($value)
 * @method static Builder<static>|ApprovalEventContributor whereApprovedAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereCancelledAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereCreatedAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereCreatedBy($value)
 * @method static Builder<static>|ApprovalEventContributor whereDeletedAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereDeletedBy($value)
 * @method static Builder<static>|ApprovalEventContributor whereId($value)
 * @method static Builder<static>|ApprovalEventContributor whereRejectedAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereRollbackAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalEventContributor whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalEventContributor whereUserId($value)
 * @method static Builder<static>|ApprovalEventContributor withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalEventContributor withoutTrashed()
 *
 * @mixin Eloquent
 */
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class ApprovalEventContributor extends Model
{
    use CreatedByTrait, DeletedByTrait, UpdatedByTrait;
    use HasUlids, SoftDeletes;

    protected $appends = [
        'is_approved',
        'is_rejected',
        'is_cancelled',
        'is_rollback',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'approval_event_component_id',
        'user_id',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'rollback_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'rollback_at' => 'datetime',
    ];

    /**
     * Get the component associated with this contributor.
     *
     * @return BelongsTo<ApprovalEventComponent, $this>
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(ApprovalEventComponent::class);
    }

    /**
     * Get the user associated with this contributor.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approval status of the event.
     *
     * @return Attribute<bool, never>
     *
     * @noinspection PhpUnused
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
     * @return Attribute<bool, never>
     *
     * @noinspection PhpUnused
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
     * @return Attribute<bool, never>
     *
     * @noinspection PhpUnused
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
     * @return Attribute<bool, never>
     *
     * @noinspection PhpUnused
     */
    protected function isRollback(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['rollback_at']),
        );
    }
}
