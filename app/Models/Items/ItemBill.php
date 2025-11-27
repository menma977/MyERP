<?php

namespace App\Models\Items;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents an Item Bill in the system.
 *
 * @property string $id
 * @property string $item_id
 * @property string $code
 * @property string $quantity
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Items\ItemBillComponent> $component
 * @property-read int|null $component_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Items\Item $item
 * @property-read User|null $updatedBy
 * @method static Builder<static>|ItemBill newModelQuery()
 * @method static Builder<static>|ItemBill newQuery()
 * @method static Builder<static>|ItemBill onlyTrashed()
 * @method static Builder<static>|ItemBill query()
 * @method static Builder<static>|ItemBill whereCode($value)
 * @method static Builder<static>|ItemBill whereCreatedAt($value)
 * @method static Builder<static>|ItemBill whereCreatedBy($value)
 * @method static Builder<static>|ItemBill whereDeletedAt($value)
 * @method static Builder<static>|ItemBill whereDeletedBy($value)
 * @method static Builder<static>|ItemBill whereId($value)
 * @method static Builder<static>|ItemBill whereItemId($value)
 * @method static Builder<static>|ItemBill whereQuantity($value)
 * @method static Builder<static>|ItemBill whereUpdatedAt($value)
 * @method static Builder<static>|ItemBill whereUpdatedBy($value)
 * @method static Builder<static>|ItemBill withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ItemBill withoutTrashed()
 * @mixin Eloquent
 */
class ItemBill extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'item_id',
		'code',
		'quantity',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<Item, $this>
	 */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'item_id');
	}

	/**
	 * @return HasMany<ItemBillComponent, $this>
	 */
	public function component(): HasMany
	{
		return $this->hasMany(ItemBillComponent::class);
	}
}
