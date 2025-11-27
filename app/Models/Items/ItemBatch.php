<?php

namespace App\Models\Items;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents an Item Batch in the system.
 *
 * @property string $id
 * @property string $item_id
 * @property string $code
 * @property Carbon|null $expiry_at
 * @property int $is_available
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Items\Item $item
 * @property-read \App\Models\Items\ItemStock|null $stock
 * @property-read User|null $updatedBy
 * @method static Builder<static>|ItemBatch newModelQuery()
 * @method static Builder<static>|ItemBatch newQuery()
 * @method static Builder<static>|ItemBatch onlyTrashed()
 * @method static Builder<static>|ItemBatch query()
 * @method static Builder<static>|ItemBatch whereCode($value)
 * @method static Builder<static>|ItemBatch whereCreatedAt($value)
 * @method static Builder<static>|ItemBatch whereCreatedBy($value)
 * @method static Builder<static>|ItemBatch whereDeletedAt($value)
 * @method static Builder<static>|ItemBatch whereDeletedBy($value)
 * @method static Builder<static>|ItemBatch whereExpiryAt($value)
 * @method static Builder<static>|ItemBatch whereId($value)
 * @method static Builder<static>|ItemBatch whereIsAvailable($value)
 * @method static Builder<static>|ItemBatch whereItemId($value)
 * @method static Builder<static>|ItemBatch whereUpdatedAt($value)
 * @method static Builder<static>|ItemBatch whereUpdatedBy($value)
 * @method static Builder<static>|ItemBatch withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ItemBatch withoutTrashed()
 * @mixin Eloquent
 */
class ItemBatch extends ModelAbstract
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
		'expiry_at',
		'is_available',
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
		return $this->belongsTo(Item::class);
	}

	/**
	 * @return HasOne<ItemStock, $this>
	 */
	public function stock(): HasOne
	{
		return $this->hasOne(ItemStock::class);
	}

	protected function casts(): array
	{
		return [
			'expiry_at' => 'datetime',
		];
	}
}
