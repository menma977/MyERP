<?php

namespace App\Models\Items;

use App\Abstracts\ModelAbstract;
use App\Enums\ItemTypeEnum;
use App\Enums\ItemUnitEnum;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents an Item in the system.
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property ItemTypeEnum $type
 * @property ItemUnitEnum $unit
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, ItemBatch> $batches
 * @property-read int|null $batches_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|Item newModelQuery()
 * @method static Builder<static>|Item newQuery()
 * @method static Builder<static>|Item onlyTrashed()
 * @method static Builder<static>|Item query()
 * @method static Builder<static>|Item whereCode($value)
 * @method static Builder<static>|Item whereCreatedAt($value)
 * @method static Builder<static>|Item whereCreatedBy($value)
 * @method static Builder<static>|Item whereDeletedAt($value)
 * @method static Builder<static>|Item whereDeletedBy($value)
 * @method static Builder<static>|Item whereId($value)
 * @method static Builder<static>|Item whereName($value)
 * @method static Builder<static>|Item whereType($value)
 * @method static Builder<static>|Item whereUnit($value)
 * @method static Builder<static>|Item whereUpdatedAt($value)
 * @method static Builder<static>|Item whereUpdatedBy($value)
 * @method static Builder<static>|Item withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Item withoutTrashed()
 *
 * @mixin Eloquent
 */
class Item extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'unit',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return HasMany<ItemBatch, $this>
     */
    public function batches(): HasMany
    {
        return $this->hasMany(ItemBatch::class);
    }

    protected function casts(): array
    {
        return [
            'type' => ItemTypeEnum::class,
            'unit' => ItemUnitEnum::class,
        ];
    }
}
