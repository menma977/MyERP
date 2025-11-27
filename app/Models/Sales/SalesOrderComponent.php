<?php

namespace App\Models\Sales;

use App\Abstracts\ModelAbstract;
use App\Models\Items\Item;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $sales_order_id
 * @property string $item_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read Item $item
 * @property-read \App\Models\Sales\SalesOrder|null $order
 * @property-read User|null $updatedBy
 * @method static Builder<static>|SalesOrderComponent newModelQuery()
 * @method static Builder<static>|SalesOrderComponent newQuery()
 * @method static Builder<static>|SalesOrderComponent onlyTrashed()
 * @method static Builder<static>|SalesOrderComponent query()
 * @method static Builder<static>|SalesOrderComponent whereCreatedAt($value)
 * @method static Builder<static>|SalesOrderComponent whereCreatedBy($value)
 * @method static Builder<static>|SalesOrderComponent whereDeletedAt($value)
 * @method static Builder<static>|SalesOrderComponent whereDeletedBy($value)
 * @method static Builder<static>|SalesOrderComponent whereId($value)
 * @method static Builder<static>|SalesOrderComponent whereItemId($value)
 * @method static Builder<static>|SalesOrderComponent wherePrice($value)
 * @method static Builder<static>|SalesOrderComponent whereQuantity($value)
 * @method static Builder<static>|SalesOrderComponent whereSalesOrderId($value)
 * @method static Builder<static>|SalesOrderComponent whereTotal($value)
 * @method static Builder<static>|SalesOrderComponent whereUpdatedAt($value)
 * @method static Builder<static>|SalesOrderComponent whereUpdatedBy($value)
 * @method static Builder<static>|SalesOrderComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesOrderComponent withoutTrashed()
 * @mixin Eloquent
 */
class SalesOrderComponent extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'sales_order_id',
		'item_id',
		'quantity',
		'price',
		'total',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<SalesOrder, $this>
	 */
	public function order(): BelongsTo
	{
		return $this->belongsTo(SalesOrder::class);
	}

	/**
	 * @return BelongsTo<Item, $this>
	 */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}
}
