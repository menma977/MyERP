<?php

namespace App\Models\Items;

use App\Abstracts\ModelAbstract;
use App\Enums\ItemTypeEnum;
use App\Enums\ItemUnitEnum;
use App\Http\Resources\Items\ItemResource;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
 * @property numeric $cost
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Items\ItemBatch> $batches
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
 * @method static Builder<static>|Item withUsers()
 * @method static Builder<static>|Item withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(ItemResource::class)]
class Item extends ModelAbstract
{
    /** @use HasFactory<\Database\Factories\Items\ItemFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'unit',
        'cost',
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

    /**
     * @return HasManyThrough<ItemStock, ItemBatch, $this>
     */
    public function stocks(): HasManyThrough
    {
        return $this->hasManyThrough(ItemStock::class, ItemBatch::class);
    }

    /**
     * Calculate and update the weighted average cost of the item.
     * This should be called whenever stock is added or removed.
     */
    public function updateWeightedAverageCost(): void
    {
        $aggregate = $this->stocks()->where('quantity', '>', 0)->selectRaw('SUM(quantity * price) as total_value, SUM(quantity) as total_quantity')->first();

        $totalValue = $aggregate->total_value ?? 0;
        $totalQuantity = $aggregate->total_quantity ?? 0;

        $this->cost = $totalQuantity > 0 ? round($totalValue / $totalQuantity, 2) : 0;

        $this->save();
    }

    protected function casts(): array
    {
        return [
            'type' => ItemTypeEnum::class,
            'unit' => ItemUnitEnum::class,
            'cost' => 'decimal:2',
        ];
    }
}
