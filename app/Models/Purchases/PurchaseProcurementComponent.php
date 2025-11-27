<?php

namespace App\Models\Purchases;

use App\Abstracts\ModelAbstract;
use App\Models\Items\Item;
use App\Models\User;
use App\Models\Vendors\Vendor;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $purchase_procurement_id
 * @property int $vendor_id
 * @property string $item_id
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
 * @property-read \App\Models\Purchases\PurchaseProcurement|null $procurement
 * @property-read User|null $updatedBy
 * @property-read Vendor $vendor
 * @method static Builder<static>|PurchaseProcurementComponent newModelQuery()
 * @method static Builder<static>|PurchaseProcurementComponent newQuery()
 * @method static Builder<static>|PurchaseProcurementComponent onlyTrashed()
 * @method static Builder<static>|PurchaseProcurementComponent query()
 * @method static Builder<static>|PurchaseProcurementComponent whereCreatedAt($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereCreatedBy($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereDeletedAt($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereDeletedBy($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereId($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereItemId($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereNote($value)
 * @method static Builder<static>|PurchaseProcurementComponent wherePrice($value)
 * @method static Builder<static>|PurchaseProcurementComponent wherePurchaseProcurementId($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereQuantity($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereTotal($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseProcurementComponent whereVendorId($value)
 * @method static Builder<static>|PurchaseProcurementComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseProcurementComponent withoutTrashed()
 * @mixin Eloquent
 */
class PurchaseProcurementComponent extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'purchase_procurement_id',
		'vendor_id',
		'item_id',
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
	 * @return BelongsTo<PurchaseProcurement, $this>
	 */
	public function procurement(): BelongsTo
	{
		return $this->belongsTo(PurchaseProcurement::class);
	}

	/**
	 * @return BelongsTo<\App\Models\Vendors\Vendor, $this>
	 */
	public function vendor(): BelongsTo
	{
		return $this->belongsTo(Vendor::class);
	}

	/**
	 * @return BelongsTo<Item, $this>
	 */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}
}
