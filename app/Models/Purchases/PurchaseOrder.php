<?php

namespace App\Models\Purchases;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents a Purchase Order in the system.
 *
 * @property string $id
 * @property string $purchase_request_id
 * @property string $purchase_procurement_id
 * @property string $code
 * @property string $request_total
 * @property string $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Purchases\PurchaseOrderComponent> $component
 * @property-read int|null $component_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read \App\Models\Purchases\PurchaseProcurement|null $procurement
 * @property-read \App\Models\Purchases\PurchaseRequest|null $request
 * @property-read \App\Models\Purchases\PurchaseReturn|null $return
 * @property-read User|null $updatedBy
 * @method static Builder<static>|PurchaseOrder newModelQuery()
 * @method static Builder<static>|PurchaseOrder newQuery()
 * @method static Builder<static>|PurchaseOrder onlyTrashed()
 * @method static Builder<static>|PurchaseOrder query()
 * @method static Builder<static>|PurchaseOrder whereCode($value)
 * @method static Builder<static>|PurchaseOrder whereCreatedAt($value)
 * @method static Builder<static>|PurchaseOrder whereCreatedBy($value)
 * @method static Builder<static>|PurchaseOrder whereDeletedAt($value)
 * @method static Builder<static>|PurchaseOrder whereDeletedBy($value)
 * @method static Builder<static>|PurchaseOrder whereId($value)
 * @method static Builder<static>|PurchaseOrder whereNote($value)
 * @method static Builder<static>|PurchaseOrder wherePurchaseProcurementId($value)
 * @method static Builder<static>|PurchaseOrder wherePurchaseRequestId($value)
 * @method static Builder<static>|PurchaseOrder whereRequestTotal($value)
 * @method static Builder<static>|PurchaseOrder whereTotal($value)
 * @method static Builder<static>|PurchaseOrder whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseOrder whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseOrder withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseOrder withoutTrashed()
 * @mixin Eloquent
 */
class PurchaseOrder extends ApprovalAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'purchase_request_id',
		'purchase_procurement_id',
		'code',
		'request_total',
		'total',
		'note',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<PurchaseRequest, $this>
	 */
	public function request(): BelongsTo
	{
		return $this->belongsTo(PurchaseRequest::class);
	}

	/**
	 * @return BelongsTo<PurchaseProcurement, $this>
	 */
	public function procurement(): BelongsTo
	{
		return $this->belongsTo(PurchaseProcurement::class);
	}

	/**
	 * @return HasOne<PurchaseReturn, $this>
	 */
	public function return(): HasOne
	{
		return $this->hasOne(PurchaseReturn::class);
	}

	/**
	 * @return HasMany<PurchaseOrderComponent, $this>
	 */
	public function component(): HasMany
	{
		return $this->hasMany(PurchaseOrderComponent::class);
	}
}
