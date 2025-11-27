<?php

namespace App\Models\Sales;

use App\Abstracts\ModelAbstract;
use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $sales_return_id
 * @property string $item_id
 * @property string $item_batch_id
 * @property string $item_stock_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ItemBatch|null $batch
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read Item $item
 * @property-read SalesReturn|null $return
 * @property-read ItemStock|null $stock
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|SalesReturnComponent newModelQuery()
 * @method static Builder<static>|SalesReturnComponent newQuery()
 * @method static Builder<static>|SalesReturnComponent onlyTrashed()
 * @method static Builder<static>|SalesReturnComponent query()
 * @method static Builder<static>|SalesReturnComponent whereCreatedAt($value)
 * @method static Builder<static>|SalesReturnComponent whereCreatedBy($value)
 * @method static Builder<static>|SalesReturnComponent whereDeletedAt($value)
 * @method static Builder<static>|SalesReturnComponent whereDeletedBy($value)
 * @method static Builder<static>|SalesReturnComponent whereId($value)
 * @method static Builder<static>|SalesReturnComponent whereItemBatchId($value)
 * @method static Builder<static>|SalesReturnComponent whereItemId($value)
 * @method static Builder<static>|SalesReturnComponent whereItemStockId($value)
 * @method static Builder<static>|SalesReturnComponent wherePrice($value)
 * @method static Builder<static>|SalesReturnComponent whereQuantity($value)
 * @method static Builder<static>|SalesReturnComponent whereSalesReturnId($value)
 * @method static Builder<static>|SalesReturnComponent whereTotal($value)
 * @method static Builder<static>|SalesReturnComponent whereUpdatedAt($value)
 * @method static Builder<static>|SalesReturnComponent whereUpdatedBy($value)
 * @method static Builder<static>|SalesReturnComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesReturnComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class SalesReturnComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sales_return_id',
        'item_id',
        'item_batch_id',
        'item_stock_id',
        'quantity',
        'price',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<SalesReturn, $this>
     */
    public function return(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    /**
     * @return BelongsTo<ItemStock, $this>
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }

    /**
     * @return BelongsTo<ItemBatch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ItemBatch::class);
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
