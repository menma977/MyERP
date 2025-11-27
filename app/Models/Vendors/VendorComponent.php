<?php

namespace App\Models\Vendors;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
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
 * @property int $vendor_id
 * @property string $item_id
 * @property string $price
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read Item $item
 * @property-read User|null $updatedBy
 * @property-read \App\Models\Vendors\Vendor $vendor
 * @method static Builder<static>|VendorComponent newModelQuery()
 * @method static Builder<static>|VendorComponent newQuery()
 * @method static Builder<static>|VendorComponent onlyTrashed()
 * @method static Builder<static>|VendorComponent query()
 * @method static Builder<static>|VendorComponent whereCreatedAt($value)
 * @method static Builder<static>|VendorComponent whereCreatedBy($value)
 * @method static Builder<static>|VendorComponent whereDeletedAt($value)
 * @method static Builder<static>|VendorComponent whereDeletedBy($value)
 * @method static Builder<static>|VendorComponent whereId($value)
 * @method static Builder<static>|VendorComponent whereItemId($value)
 * @method static Builder<static>|VendorComponent wherePrice($value)
 * @method static Builder<static>|VendorComponent whereUpdatedAt($value)
 * @method static Builder<static>|VendorComponent whereUpdatedBy($value)
 * @method static Builder<static>|VendorComponent whereVendorId($value)
 * @method static Builder<static>|VendorComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorComponent withoutTrashed()
 * @mixin Eloquent
 */
class VendorComponent extends ApprovalAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'vendor_id',
		'item_id',
		'price',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<Vendor, $this>
	 */
	public function vendor(): BelongsTo
	{
		return $this->belongsTo(Vendor::class, 'vendor_id');
	}

	/**
	 * @return BelongsTo<Item, $this>
	 */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'item_id');
	}
}
