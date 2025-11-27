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
 * Represents an Item Stock History in the system.
 *
 * @property string $id
 * @property string $item_stock_id
 * @property string $code
 * @property string $quantity
 * @property string $price
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Items\ItemStock|null $stock
 * @property-read User|null $updatedBy
 * @method static Builder<static>|ItemStockHistory newModelQuery()
 * @method static Builder<static>|ItemStockHistory newQuery()
 * @method static Builder<static>|ItemStockHistory onlyTrashed()
 * @method static Builder<static>|ItemStockHistory query()
 * @method static Builder<static>|ItemStockHistory whereCode($value)
 * @method static Builder<static>|ItemStockHistory whereCreatedAt($value)
 * @method static Builder<static>|ItemStockHistory whereCreatedBy($value)
 * @method static Builder<static>|ItemStockHistory whereDeletedAt($value)
 * @method static Builder<static>|ItemStockHistory whereDeletedBy($value)
 * @method static Builder<static>|ItemStockHistory whereId($value)
 * @method static Builder<static>|ItemStockHistory whereItemStockId($value)
 * @method static Builder<static>|ItemStockHistory wherePrice($value)
 * @method static Builder<static>|ItemStockHistory whereQuantity($value)
 * @method static Builder<static>|ItemStockHistory whereUpdatedAt($value)
 * @method static Builder<static>|ItemStockHistory whereUpdatedBy($value)
 * @method static Builder<static>|ItemStockHistory withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ItemStockHistory withoutTrashed()
 * @mixin Eloquent
 */
class ItemStockHistory extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'item_stock_id',
		'code',
		'quantity',
		'price',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<ItemStock, $this>
	 */
	public function stock(): BelongsTo
	{
		return $this->belongsTo(ItemStock::class);
	}
}
