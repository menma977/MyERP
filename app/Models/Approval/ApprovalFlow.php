<?php

namespace App\Models\Approval;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Approval|null $approval
 * @property-read Collection<int, ApprovalFlowComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|ApprovalFlow newModelQuery()
 * @method static Builder<static>|ApprovalFlow newQuery()
 * @method static Builder<static>|ApprovalFlow onlyTrashed()
 * @method static Builder<static>|ApprovalFlow query()
 * @method static Builder<static>|ApprovalFlow whereCreatedAt($value)
 * @method static Builder<static>|ApprovalFlow whereCreatedBy($value)
 * @method static Builder<static>|ApprovalFlow whereDeletedAt($value)
 * @method static Builder<static>|ApprovalFlow whereDeletedBy($value)
 * @method static Builder<static>|ApprovalFlow whereId($value)
 * @method static Builder<static>|ApprovalFlow whereName($value)
 * @method static Builder<static>|ApprovalFlow whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalFlow whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalFlow withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalFlow withoutTrashed()
 *
 * @mixin Eloquent
 */
class ApprovalFlow extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the approval associated with this flow.
     *
     * @return HasOne<Approval, $this>
     */
    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }

    /**
     * Get the components associated with this flow.
     *
     * @return HasMany<ApprovalFlowComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(ApprovalFlowComponent::class);
    }
}
