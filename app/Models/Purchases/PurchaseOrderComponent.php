<?php

namespace App\Models\Purchases;

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
 * @property string $purchase_order_id
 * @property string $purchase_request_component_id
 * @property string $purchase_procurement_component_id
 * @property string $item_id
 * @property string $request_quantity
 * @property string $request_price
 * @property string $request_total
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read Item $item
 * @property-read \App\Models\Purchases\PurchaseOrder|null $order
 * @property-read \App\Models\Purchases\PurchaseProcurementComponent|null $procurementComponent
 * @property-read \App\Models\Purchases\PurchaseRequestComponent|null $requestComponent
 * @property-read User|null $updatedBy
 * @method static Builder<static>|PurchaseOrderComponent newModelQuery()
 * @method static Builder<static>|PurchaseOrderComponent newQuery()
 * @method static Builder<static>|PurchaseOrderComponent onlyTrashed()
 * @method static Builder<static>|PurchaseOrderComponent query()
 * @method static Builder<static>|PurchaseOrderComponent whereCreatedAt($value)
 * @method static Builder<static>|PurchaseOrderComponent whereCreatedBy($value)
 * @method static Builder<static>|PurchaseOrderComponent whereDeletedAt($value)
 * @method static Builder<static>|PurchaseOrderComponent whereDeletedBy($value)
 * @method static Builder<static>|PurchaseOrderComponent whereId($value)
 * @method static Builder<static>|PurchaseOrderComponent whereItemId($value)
 * @method static Builder<static>|PurchaseOrderComponent whereNote($value)
 * @method static Builder<static>|PurchaseOrderComponent wherePrice($value)
 * @method static Builder<static>|PurchaseOrderComponent wherePurchaseOrderId($value)
 * @method static Builder<static>|PurchaseOrderComponent wherePurchaseProcurementComponentId($value)
 * @method static Builder<static>|PurchaseOrderComponent wherePurchaseRequestComponentId($value)
 * @method static Builder<static>|PurchaseOrderComponent whereQuantity($value)
 * @method static Builder<static>|PurchaseOrderComponent whereRequestPrice($value)
 * @method static Builder<static>|PurchaseOrderComponent whereRequestQuantity($value)
 * @method static Builder<static>|PurchaseOrderComponent whereRequestTotal($value)
 * @method static Builder<static>|PurchaseOrderComponent whereTotal($value)
 * @method static Builder<static>|PurchaseOrderComponent whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseOrderComponent whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseOrderComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseOrderComponent withoutTrashed()
 * @mixin Eloquent
 */
class PurchaseOrderComponent extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'purchase_order_id',
		'purchase_request_component_id',
		'purchase_procurement_component_id',
		'item_id',
		'request_quantity',
		'request_price',
		'request_total',
		'quantity',
		'price',
		'total',
		'note',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<PurchaseOrder, $this>
	 */
	public function order(): BelongsTo
	{
		return $this->belongsTo(PurchaseOrder::class);
	}

	/**
	 * @return BelongsTo<PurchaseRequestComponent, $this>
	 */
	public function requestComponent(): BelongsTo
	{
		return $this->belongsTo(PurchaseRequestComponent::class);
	}

	/**
	 * @return BelongsTo<PurchaseProcurementComponent, $this>
	 */
	public function procurementComponent(): BelongsTo
	{
		return $this->belongsTo(PurchaseProcurementComponent::class);
	}

	/**
	 * @return BelongsTo<Item, $this>
	 */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}
}
