<?php

namespace App\Models\Approval;

use App\Abstracts\ModelAbstract;
use App\Enums\ContributorTypeEnum;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $approval_event_id
 * @property string $name
 * @property int $step The step using binary 1 -> 10 -> 100 -> 1000
 * @property ContributorTypeEnum $type The type of approval logic (0:and/1:or)
 * @property string $color
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
 * @property-read Collection<int, ApprovalEventContributor> $contributors
 * @property-read int|null $contributors_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent $event
 * @property-read mixed $is_approved
 * @property-read mixed $is_cancelled
 * @property-read mixed $is_rejected
 * @property-read mixed $is_rollback
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|ApprovalEventComponent newModelQuery()
 * @method static Builder<static>|ApprovalEventComponent newQuery()
 * @method static Builder<static>|ApprovalEventComponent onlyTrashed()
 * @method static Builder<static>|ApprovalEventComponent query()
 * @method static Builder<static>|ApprovalEventComponent whereApprovalEventId($value)
 * @method static Builder<static>|ApprovalEventComponent whereApprovedAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereCancelledAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereColor($value)
 * @method static Builder<static>|ApprovalEventComponent whereCreatedAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereCreatedBy($value)
 * @method static Builder<static>|ApprovalEventComponent whereDeletedAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereDeletedBy($value)
 * @method static Builder<static>|ApprovalEventComponent whereId($value)
 * @method static Builder<static>|ApprovalEventComponent whereName($value)
 * @method static Builder<static>|ApprovalEventComponent whereRejectedAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereRollbackAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereStep($value)
 * @method static Builder<static>|ApprovalEventComponent whereType($value)
 * @method static Builder<static>|ApprovalEventComponent whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalEventComponent whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalEventComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalEventComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class ApprovalEventComponent extends ModelAbstract
{
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
        'approval_event_id',
        'name',
        'step',
        'type',
        'color',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'rollback_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'step' => 'integer',
        'type' => ContributorTypeEnum::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'rollback_at' => 'datetime',
    ];

    /**
     * Get the event associated with this component.
     *
     * @return BelongsTo<ApprovalEvent, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(ApprovalEvent::class, 'approval_event_id');
    }

    /**
     * Get the contributors associated with this component.
     *
     * @return HasMany<ApprovalEventContributor, $this>
     */
    public function contributors(): HasMany
    {
        return $this->hasMany(ApprovalEventContributor::class);
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
            get: fn (mixed $value, array $attributes) => isset($attributes['approved_at']),
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
            get: fn (mixed $value, array $attributes) => isset($attributes['rejected_at']),
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
            get: fn (mixed $value, array $attributes) => isset($attributes['cancelled_at']),
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
            get: fn (mixed $value, array $attributes) => isset($attributes['rollback_at']),
        );
    }
}
