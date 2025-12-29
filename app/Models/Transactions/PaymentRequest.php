<?php

namespace App\Models\Transactions;

use App\Abstracts\ApprovalAbstract;
use App\Enums\PaymentMethodEnum;
use App\Models\Approval\ApprovalEvent;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseOrder;
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
 * Represents a Payment Request in the system.
 *
 * @property string $id
 * @property string $purchase_order_id
 * @property string $purchase_invoice_id
 * @property string $code
 * @property PaymentMethodEnum $method
 * @property float $total
 * @property float $tax
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PaymentRequestComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read PurchaseInvoice|null $invoice
 * @property-read PurchaseOrder|null $order
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PaymentRequest newModelQuery()
 * @method static Builder<static>|PaymentRequest newQuery()
 * @method static Builder<static>|PaymentRequest onlyTrashed()
 * @method static Builder<static>|PaymentRequest query()
 * @method static Builder<static>|PaymentRequest whereCode($value)
 * @method static Builder<static>|PaymentRequest whereCreatedAt($value)
 * @method static Builder<static>|PaymentRequest whereCreatedBy($value)
 * @method static Builder<static>|PaymentRequest whereDeletedAt($value)
 * @method static Builder<static>|PaymentRequest whereDeletedBy($value)
 * @method static Builder<static>|PaymentRequest whereId($value)
 * @method static Builder<static>|PaymentRequest whereMethod($value)
 * @method static Builder<static>|PaymentRequest whereNote($value)
 * @method static Builder<static>|PaymentRequest wherePurchaseInvoiceId($value)
 * @method static Builder<static>|PaymentRequest wherePurchaseOrderId($value)
 * @method static Builder<static>|PaymentRequest whereTax($value)
 * @method static Builder<static>|PaymentRequest whereTotal($value)
 * @method static Builder<static>|PaymentRequest whereUpdatedAt($value)
 * @method static Builder<static>|PaymentRequest whereUpdatedBy($value)
 * @method static Builder<static>|PaymentRequest withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PaymentRequest withoutTrashed()
 *
 * @mixin Eloquent
 */
class PaymentRequest extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_order_id',
        'purchase_invoice_id',
        'code',
        'method',
        'total',
        'tax',
        'note',
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
     * @return BelongsTo<PurchaseInvoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * @return HasMany<PaymentRequestComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(PaymentRequestComponent::class, 'payment_request_id');
    }

    protected function casts(): array
    {
        return [
            'method' => PaymentMethodEnum::class,
        ];
    }
}
