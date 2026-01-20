<?php

namespace App\Models\Approval;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $key
 * @property string $name
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, ApprovalFlowComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|ApprovalDictionary newModelQuery()
 * @method static Builder<static>|ApprovalDictionary newQuery()
 * @method static Builder<static>|ApprovalDictionary onlyTrashed()
 * @method static Builder<static>|ApprovalDictionary query()
 * @method static Builder<static>|ApprovalDictionary whereCreatedAt($value)
 * @method static Builder<static>|ApprovalDictionary whereCreatedBy($value)
 * @method static Builder<static>|ApprovalDictionary whereDeletedAt($value)
 * @method static Builder<static>|ApprovalDictionary whereDeletedBy($value)
 * @method static Builder<static>|ApprovalDictionary whereId($value)
 * @method static Builder<static>|ApprovalDictionary whereKey($value)
 * @method static Builder<static>|ApprovalDictionary whereName($value)
 * @method static Builder<static>|ApprovalDictionary whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalDictionary whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalDictionary withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalDictionary withoutTrashed()
 *
 * @mixin Eloquent
 */
class ApprovalDictionary extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'key',
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the components associated with this dictionary.
     *
     * @return HasMany<ApprovalFlowComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(ApprovalFlowComponent::class);
    }
}
