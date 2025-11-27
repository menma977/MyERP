<?php

namespace App\Models\Sales;

use App\Abstracts\ModelAbstract;
use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $sales_invoice_id
 * @property string $item_id
 * @property string $item_batch_id
 * @property string $item_stock_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ItemBatch|null $batch
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Sales\SalesInvoice|null $invoice
 * @property-read Item $item
 * @property-read ItemStock|null $stock
 * @property-read User|null $updatedBy
 * @method static Builder<static>|SalesInvoiceComponent newModelQuery()
 * @method static Builder<static>|SalesInvoiceComponent newQuery()
 * @method static Builder<static>|SalesInvoiceComponent onlyTrashed()
 * @method static Builder<static>|SalesInvoiceComponent query()
 * @method static Builder<static>|SalesInvoiceComponent whereCreatedAt($value)
 * @method static Builder<static>|SalesInvoiceComponent whereCreatedBy($value)
 * @method static Builder<static>|SalesInvoiceComponent whereDeletedAt($value)
 * @method static Builder<static>|SalesInvoiceComponent whereDeletedBy($value)
 * @method static Builder<static>|SalesInvoiceComponent whereId($value)
 * @method static Builder<static>|SalesInvoiceComponent whereItemBatchId($value)
 * @method static Builder<static>|SalesInvoiceComponent whereItemId($value)
 * @method static Builder<static>|SalesInvoiceComponent whereItemStockId($value)
 * @method static Builder<static>|SalesInvoiceComponent wherePrice($value)
 * @method static Builder<static>|SalesInvoiceComponent whereQuantity($value)
 * @method static Builder<static>|SalesInvoiceComponent whereSalesInvoiceId($value)
 * @method static Builder<static>|SalesInvoiceComponent whereTotal($value)
 * @method static Builder<static>|SalesInvoiceComponent whereUpdatedAt($value)
 * @method static Builder<static>|SalesInvoiceComponent whereUpdatedBy($value)
 * @method static Builder<static>|SalesInvoiceComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesInvoiceComponent withoutTrashed()
 * @mixin Eloquent
 */
class SalesInvoiceComponent extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'sales_invoice_id',
		'item_id',
		'item_batch_id',
		'item_stock_id',
		'quantity',
		'price',
		'total',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<SalesInvoice, $this>
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(SalesInvoice::class);
	}

	/**
	 * @return BelongsTo<Item, $this>
	 */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	/**
	 * @return BelongsTo<ItemStock, $this>
	 */
	public function stock(): BelongsTo
	{
		return $this->belongsTo(ItemStock::class);
	}

	/**
	 * @return BelongsTo<ItemBatch, $this>
	 */
	public function batch(): BelongsTo
	{
		return $this->belongsTo(ItemBatch::class);
	}
}
