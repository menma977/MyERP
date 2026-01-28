<?php

namespace App\Models\Purchases;

use App\Abstracts\ApprovalAbstract;
use App\Enums\PaymentMethodEnum;
use App\Http\Resources\Purchases\PurchaseInvoiceResource;
use App\Models\Approval\ApprovalEvent;
use App\Models\Transactions\PaymentRequest;
use App\Models\Transactions\PaymentRequestComponent;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Database\Factories\Purchases\PurchaseInvoiceFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Represents a Pro form Invoice in the system.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $purchase_order_id
 * @property string $code
 * @property numeric $total
 * @property numeric $tax
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
 * @property-read \App\Models\Purchases\PurchaseOrder $order
 * @property-read User|null $updatedBy
 *
 * @method static PurchaseInvoiceFactory factory($count = null, $state = [])
 * @method static Builder<static>|PurchaseInvoice newModelQuery()
 * @method static Builder<static>|PurchaseInvoice newQuery()
 * @method static Builder<static>|PurchaseInvoice onlyTrashed()
 * @method static Builder<static>|PurchaseInvoice query()
 * @method static Builder<static>|PurchaseInvoice whereCode($value)
 * @method static Builder<static>|PurchaseInvoice whereCompanyId($value)
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
 * @method static Builder<static>|PurchaseInvoice withContributors()
 * @method static Builder<static>|PurchaseInvoice withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseInvoice withUsers()
 * @method static Builder<static>|PurchaseInvoice withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(PurchaseInvoiceResource::class)]
class PurchaseInvoice extends ApprovalAbstract
{
    /** @use HasFactory<\Database\Factories\Purchases\PurchaseInvoiceFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    const float TAX = 0.12;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
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
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * @return HasMany<PurchaseInvoiceComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceComponent::class, 'purchase_invoice_id');
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'tax' => 'decimal:2',
        ];
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $purchaseInvoice = PurchaseInvoice::lockForUpdate()->with('components')->find($approvalEvent->requestable_id);
                if (! $purchaseInvoice) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Purchase Invoice']),
                    ]);
                }

                $payment = new PaymentRequest;
                $payment->purchase_invoice_id = $purchaseInvoice->id;
                $payment->purchase_order_id = $purchaseInvoice->purchase_order_id;
                $payment->code = CodeGeneratorService::code('PYR')->number(PaymentRequest::count())->generate();
                $payment->total = $purchaseInvoice->total;
                $payment->tax = $purchaseInvoice->tax;
                $payment->method = PaymentMethodEnum::BANK_TRANSFER;
                $payment->note = $purchaseInvoice->order->note;
                $payment->save();

                foreach ($purchaseInvoice->components as $component) {
                    $paymentComponent = new PaymentRequestComponent;
                    $paymentComponent->payment_request_id = $payment->id;
                    $paymentComponent->purchase_invoice_component_id = $component->id;
                    $paymentComponent->purchase_order_component_id = $component->purchase_order_component_id;
                    $paymentComponent->quantity = $component->quantity;
                    $paymentComponent->price = $component->price;
                    $paymentComponent->total = $component->total;
                    $paymentComponent->save();
                }
            });
        }
    }
}
