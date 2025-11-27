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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents a Pro forma Invoice in the system.
 *
 * @property string $id
 * @property string $purchase_order_id
 * @property string $code
 * @property string $total
 * @property string $tax
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Purchases\PurchaseInvoiceComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read \App\Models\Purchases\PurchaseOrder|null $order
 * @property-read User|null $updatedBy
 * @method static Builder<static>|PurchaseInvoice newModelQuery()
 * @method static Builder<static>|PurchaseInvoice newQuery()
 * @method static Builder<static>|PurchaseInvoice onlyTrashed()
 * @method static Builder<static>|PurchaseInvoice query()
 * @method static Builder<static>|PurchaseInvoice whereCode($value)
 * @method static Builder<static>|PurchaseInvoice whereCreatedAt($value)
 * @method static Builder<static>|PurchaseInvoice whereCreatedBy($value)
 * @method static Builder<static>|PurchaseInvoice whereDeletedAt($value)
 * @method static Builder<static>|PurchaseInvoice whereDeletedBy($value)
 * @method static Builder<static>|PurchaseInvoice whereId($value)
 * @method static Builder<static>|PurchaseInvoice wherePurchaseOrderId($value)
 * @method static Builder<static>|PurchaseInvoice whereTax($value)
 * @method static Builder<static>|PurchaseInvoice whereTotal($value)
 * @method static Builder<static>|PurchaseInvoice whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseInvoice whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseInvoice withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseInvoice withoutTrashed()
 * @mixin Eloquent
 */
class PurchaseInvoice extends ApprovalAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'purchase_order_id',
		'code',
		'total',
		'tax',
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
	 * @return HasMany<PurchaseInvoiceComponent, $this>
	 */
	public function components(): HasMany
	{
		return $this->hasMany(PurchaseInvoiceComponent::class, 'purchase_invoice_id');
	}
}
