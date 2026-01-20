<?php

namespace App\Models\Transactions;

use App\Abstracts\ModelAbstract;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $payment_request_id
 * @property string $purchase_order_component_id
 * @property string $purchase_invoice_component_id
 * @property float $quantity
 * @property float $price
 * @property float $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read PaymentRequest $paymentRequest
 * @property-read PurchaseInvoiceComponent $purchaseInvoiceComponent
 * @property-read PurchaseOrderComponent $purchaseOrderComponent
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PaymentRequestComponent newModelQuery()
 * @method static Builder<static>|PaymentRequestComponent newQuery()
 * @method static Builder<static>|PaymentRequestComponent onlyTrashed()
 * @method static Builder<static>|PaymentRequestComponent query()
 * @method static Builder<static>|PaymentRequestComponent whereCreatedAt($value)
 * @method static Builder<static>|PaymentRequestComponent whereCreatedBy($value)
 * @method static Builder<static>|PaymentRequestComponent whereDeletedAt($value)
 * @method static Builder<static>|PaymentRequestComponent whereDeletedBy($value)
 * @method static Builder<static>|PaymentRequestComponent whereId($value)
 * @method static Builder<static>|PaymentRequestComponent whereNote($value)
 * @method static Builder<static>|PaymentRequestComponent wherePaymentRequestId($value)
 * @method static Builder<static>|PaymentRequestComponent wherePrice($value)
 * @method static Builder<static>|PaymentRequestComponent wherePurchaseInvoiceComponentId($value)
 * @method static Builder<static>|PaymentRequestComponent wherePurchaseOrderComponentId($value)
 * @method static Builder<static>|PaymentRequestComponent whereQuantity($value)
 * @method static Builder<static>|PaymentRequestComponent whereTotal($value)
 * @method static Builder<static>|PaymentRequestComponent whereUpdatedAt($value)
 * @method static Builder<static>|PaymentRequestComponent whereUpdatedBy($value)
 * @method static Builder<static>|PaymentRequestComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PaymentRequestComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class PaymentRequestComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'payment_request_id',
        'purchase_order_component_id',
        'purchase_invoice_component_id',
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
     * @return BelongsTo<PaymentRequest, $this>
     */
    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    /**
     * @return BelongsTo<PurchaseOrderComponent, $this>
     */
    public function purchaseOrderComponent(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderComponent::class);
    }

    /**
     * @return BelongsTo<PurchaseInvoiceComponent, $this>
     */
    public function purchaseInvoiceComponent(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoiceComponent::class);
    }
}
