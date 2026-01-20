<?php

namespace App\Models\Transactions;

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
 * Represents a Ledger in the system.
 *
 * @property string $id
 * @property string $code
 * @property string $in
 * @property string $out
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, LedgerComponent> $component
 * @property-read int|null $component_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|Ledger newModelQuery()
 * @method static Builder<static>|Ledger newQuery()
 * @method static Builder<static>|Ledger onlyTrashed()
 * @method static Builder<static>|Ledger query()
 * @method static Builder<static>|Ledger whereCode($value)
 * @method static Builder<static>|Ledger whereCreatedAt($value)
 * @method static Builder<static>|Ledger whereCreatedBy($value)
 * @method static Builder<static>|Ledger whereDeletedAt($value)
 * @method static Builder<static>|Ledger whereDeletedBy($value)
 * @method static Builder<static>|Ledger whereId($value)
 * @method static Builder<static>|Ledger whereIn($value)
 * @method static Builder<static>|Ledger whereOut($value)
 * @method static Builder<static>|Ledger whereTotal($value)
 * @method static Builder<static>|Ledger whereUpdatedAt($value)
 * @method static Builder<static>|Ledger whereUpdatedBy($value)
 * @method static Builder<static>|Ledger withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Ledger withoutTrashed()
 *
 * @mixin Eloquent
 */
class Ledger extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'in',
        'out',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return HasMany<LedgerComponent, $this>
     */
    public function component(): HasMany
    {
        return $this->hasMany(LedgerComponent::class);
    }
}
