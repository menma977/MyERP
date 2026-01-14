<?php

namespace App\Models\Items;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents an Item Stock in the system.
 *
 * @property string $id
 * @property string $item_batch_id
 * @property string $quantity
 * @property string $price
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ItemBatch|null $batch
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|ItemStock newModelQuery()
 * @method static Builder<static>|ItemStock newQuery()
 * @method static Builder<static>|ItemStock onlyTrashed()
 * @method static Builder<static>|ItemStock query()
 * @method static Builder<static>|ItemStock whereCreatedAt($value)
 * @method static Builder<static>|ItemStock whereCreatedBy($value)
 * @method static Builder<static>|ItemStock whereDeletedAt($value)
 * @method static Builder<static>|ItemStock whereDeletedBy($value)
 * @method static Builder<static>|ItemStock whereId($value)
 * @method static Builder<static>|ItemStock whereItemBatchId($value)
 * @method static Builder<static>|ItemStock wherePrice($value)
 * @method static Builder<static>|ItemStock whereQuantity($value)
 * @method static Builder<static>|ItemStock whereUpdatedAt($value)
 * @method static Builder<static>|ItemStock whereUpdatedBy($value)
 * @method static Builder<static>|ItemStock withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ItemStock withoutTrashed()
 *
 * @mixin Eloquent
 */
class ItemStock extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_batch_id',
        'quantity',
        'price',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<ItemBatch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ItemBatch::class);
    }
}
