<?php

namespace App\Models\Purchases;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents a Purchase Request in the system.
 *
 * @property string $id
 * @property string $code
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Purchases\PurchaseRequestComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read \App\Models\Purchases\PurchaseOrder|null $order
 * @property-read \App\Models\Purchases\PurchaseProcurement|null $procurement
 * @property-read User|null $updatedBy
 * @method static Builder<static>|PurchaseRequest newModelQuery()
 * @method static Builder<static>|PurchaseRequest newQuery()
 * @method static Builder<static>|PurchaseRequest onlyTrashed()
 * @method static Builder<static>|PurchaseRequest query()
 * @method static Builder<static>|PurchaseRequest whereCode($value)
 * @method static Builder<static>|PurchaseRequest whereCreatedAt($value)
 * @method static Builder<static>|PurchaseRequest whereCreatedBy($value)
 * @method static Builder<static>|PurchaseRequest whereDeletedAt($value)
 * @method static Builder<static>|PurchaseRequest whereDeletedBy($value)
 * @method static Builder<static>|PurchaseRequest whereId($value)
 * @method static Builder<static>|PurchaseRequest whereTotal($value)
 * @method static Builder<static>|PurchaseRequest whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseRequest whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseRequest withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseRequest withoutTrashed()
 * @mixin Eloquent
 */
class PurchaseRequest extends ApprovalAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'code',
		'total',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return HasOne<PurchaseOrder, $this>
	 */
	public function order(): HasOne
	{
		return $this->hasOne(PurchaseOrder::class);
	}

	/**
	 * @return HasOne<PurchaseProcurement, $this>
	 */
	public function procurement(): HasOne
	{
		return $this->hasOne(PurchaseProcurement::class);
	}

	/**
	 * @return HasMany<PurchaseRequestComponent, $this>
	 */
	public function components(): HasMany
	{
		return $this->hasMany(PurchaseRequestComponent::class);
	}
}
