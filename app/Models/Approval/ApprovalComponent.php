<?php

namespace App\Models\Approval;

use App\Abstracts\ModelAbstract;
use App\Enums\ContributorTypeEnum;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $approval_id
 * @property string $name
 * @property int $step The step using binary system: 1, 2, 3, 4, etc.
 * @property ContributorTypeEnum $type The type of approval logic (0:and/1:or)
 * @property string $color The color of the component
 * @property bool $can_drag
 * @property bool $can_edit
 * @property bool $can_delete
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Approval $approval
 * @property-read Collection<int, ApprovalContributor> $contributors
 * @property-read int|null $contributors_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|ApprovalComponent newModelQuery()
 * @method static Builder<static>|ApprovalComponent newQuery()
 * @method static Builder<static>|ApprovalComponent onlyTrashed()
 * @method static Builder<static>|ApprovalComponent query()
 * @method static Builder<static>|ApprovalComponent whereApprovalId($value)
 * @method static Builder<static>|ApprovalComponent whereCanDelete($value)
 * @method static Builder<static>|ApprovalComponent whereCanDrag($value)
 * @method static Builder<static>|ApprovalComponent whereCanEdit($value)
 * @method static Builder<static>|ApprovalComponent whereColor($value)
 * @method static Builder<static>|ApprovalComponent whereCreatedAt($value)
 * @method static Builder<static>|ApprovalComponent whereCreatedBy($value)
 * @method static Builder<static>|ApprovalComponent whereDeletedAt($value)
 * @method static Builder<static>|ApprovalComponent whereDeletedBy($value)
 * @method static Builder<static>|ApprovalComponent whereId($value)
 * @method static Builder<static>|ApprovalComponent whereName($value)
 * @method static Builder<static>|ApprovalComponent whereStep($value)
 * @method static Builder<static>|ApprovalComponent whereType($value)
 * @method static Builder<static>|ApprovalComponent whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalComponent whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class ApprovalComponent extends ModelAbstract
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'approval_id',
        'name',
        'step',
        'type',
        'color',
        'can_drag',
        'can_edit',
        'can_delete',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'step' => 'integer',
        'type' => ContributorTypeEnum::class,
        'can_drag' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
    ];

    /**
     * Get the approval associated with this component.
     *
     * @return BelongsTo<Approval, $this>
     */
    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }

    /**
     * Get the contributors associated with this component.
     *
     * @return HasMany<ApprovalContributor, $this>
     */
    public function contributors(): HasMany
    {
        return $this->hasMany(ApprovalContributor::class);
    }
}
