<?php

namespace App\Models\Sales;

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
 * Represents a Sales Return in the system.
 *
 * @property string $id
 * @property string $sales_order_id
 * @property string $sales_invoice_id
 * @property string $code
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Sales\SalesReturnComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read \App\Models\Sales\SalesInvoice|null $invoice
 * @property-read \App\Models\Sales\SalesOrder|null $order
 * @property-read User|null $updatedBy
 * @method static Builder<static>|SalesReturn newModelQuery()
 * @method static Builder<static>|SalesReturn newQuery()
 * @method static Builder<static>|SalesReturn onlyTrashed()
 * @method static Builder<static>|SalesReturn query()
 * @method static Builder<static>|SalesReturn whereCode($value)
 * @method static Builder<static>|SalesReturn whereCreatedAt($value)
 * @method static Builder<static>|SalesReturn whereCreatedBy($value)
 * @method static Builder<static>|SalesReturn whereDeletedAt($value)
 * @method static Builder<static>|SalesReturn whereDeletedBy($value)
 * @method static Builder<static>|SalesReturn whereId($value)
 * @method static Builder<static>|SalesReturn whereSalesInvoiceId($value)
 * @method static Builder<static>|SalesReturn whereSalesOrderId($value)
 * @method static Builder<static>|SalesReturn whereTotal($value)
 * @method static Builder<static>|SalesReturn whereUpdatedAt($value)
 * @method static Builder<static>|SalesReturn whereUpdatedBy($value)
 * @method static Builder<static>|SalesReturn withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesReturn withoutTrashed()
 * @mixin Eloquent
 */
class SalesReturn extends ApprovalAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'sales_order_id',
		'sales_invoice_id',
		'code',
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
	 * @return BelongsTo<SalesInvoice, $this>
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(SalesInvoice::class);
	}

	/**
	 * @return HasMany<SalesReturnComponent, $this>
	 */
	public function components(): HasMany
	{
		return $this->hasMany(SalesReturnComponent::class, 'sales_return_id');
	}
}
