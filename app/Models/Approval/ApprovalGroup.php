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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read Collection<int, ApprovalGroupContributor> $contributors
 * @property-read int|null $contributors_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|ApprovalGroup newModelQuery()
 * @method static Builder<static>|ApprovalGroup newQuery()
 * @method static Builder<static>|ApprovalGroup onlyTrashed()
 * @method static Builder<static>|ApprovalGroup query()
 * @method static Builder<static>|ApprovalGroup whereCreatedAt($value)
 * @method static Builder<static>|ApprovalGroup whereCreatedBy($value)
 * @method static Builder<static>|ApprovalGroup whereDeletedAt($value)
 * @method static Builder<static>|ApprovalGroup whereDeletedBy($value)
 * @method static Builder<static>|ApprovalGroup whereId($value)
 * @method static Builder<static>|ApprovalGroup whereName($value)
 * @method static Builder<static>|ApprovalGroup whereUpdatedAt($value)
 * @method static Builder<static>|ApprovalGroup whereUpdatedBy($value)
 * @method static Builder<static>|ApprovalGroup withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ApprovalGroup withoutTrashed()
 *
 * @mixin Eloquent
 */
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class ApprovalGroup extends Model
{
    use CreatedByTrait, DeletedByTrait, UpdatedByTrait;
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the contributors associated with this group.
     *
     * @return HasMany<ApprovalGroupContributor, $this>
     */
    public function contributors(): HasMany
    {
        return $this->hasMany(ApprovalGroupContributor::class);
    }
}
