<?php

namespace App\Models\Purchases;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $purchase_invoice_id
 * @property string $purchase_order_component_id
 * @property string $item_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read PurchaseInvoice|null $invoice
 * @property-read PurchaseOrderComponent|null $orderComponent
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PurchaseInvoiceComponent newModelQuery()
 * @method static Builder<static>|PurchaseInvoiceComponent newQuery()
 * @method static Builder<static>|PurchaseInvoiceComponent onlyTrashed()
 * @method static Builder<static>|PurchaseInvoiceComponent query()
 * @method static Builder<static>|PurchaseInvoiceComponent whereCreatedAt($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereCreatedBy($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereDeletedAt($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereDeletedBy($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereId($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereItemId($value)
 * @method static Builder<static>|PurchaseInvoiceComponent wherePrice($value)
 * @method static Builder<static>|PurchaseInvoiceComponent wherePurchaseInvoiceId($value)
 * @method static Builder<static>|PurchaseInvoiceComponent wherePurchaseOrderComponentId($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereQuantity($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereTotal($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseInvoiceComponent whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseInvoiceComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseInvoiceComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseInvoiceComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_invoice_id',
        'purchase_order_component_id',
        'item_id',
        'quantity',
        'price',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<PurchaseInvoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * @return BelongsTo<PurchaseOrderComponent, $this>
     */
    public function orderComponent(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderComponent::class);
    }
}
