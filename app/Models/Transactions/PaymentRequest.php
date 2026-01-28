<?php

namespace App\Models\Transactions;

use App\Abstracts\ApprovalAbstract;
use App\Enums\PaymentMethodEnum;
use App\Http\Resources\Transactions\PaymentRequestResource;
use App\Models\Approval\ApprovalEvent;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseOrder;
use App\Models\User;
use App\Services\CodeGeneratorService;
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
 * Represents a Payment Request in the system.
 *
 * @property string $id
 * @property string $purchase_order_id
 * @property string $purchase_invoice_id
 * @property string $code
 * @property PaymentMethodEnum $method
 * @property numeric $total
 * @property numeric $tax
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Transactions\PaymentRequestComponent> $components
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
 * @method static Builder<static>|PaymentRequest withContributors()
 * @method static Builder<static>|PaymentRequest withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PaymentRequest withUsers()
 * @method static Builder<static>|PaymentRequest withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(PaymentRequestResource::class)]
class PaymentRequest extends ApprovalAbstract
{
    /** @use HasFactory<\Database\Factories\Transactions\PaymentRequestFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
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
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * @return BelongsTo<PurchaseInvoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
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
            'total' => 'decimal:2',
            'tax' => 'decimal:2',
        ];
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $paymentRequest = PaymentRequest::lockForUpdate()->with('components')->find($approvalEvent->requestable_id);
                if (! $paymentRequest) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Payment Request']),
                    ]);
                }

                $ledger = new Ledger;
                $ledger->code = CodeGeneratorService::code('LDG-PYR')->number(Ledger::count())->generate();
                $ledger->in = 0.0;
                $ledger->out = 0.0;
                $ledger->total = 0.0;
                $ledger->save();

                $total = 0;
                foreach ($paymentRequest->components as $component) {
                    $ledgerComponent = new LedgerComponent;
                    $ledgerComponent->ledger_id = $ledger->id;
                    $ledgerComponent->in = 0.0;
                    $ledgerComponent->out = $component->total;
                    $ledgerComponent->total = $component->total;
                    $ledgerComponent->save();

                    $total += (float) $ledgerComponent->out;
                }

                $ledger->out = $total;
                $ledger->total = Ledger::sum('total') - $ledger->out;
                $ledger->save();
            });
        }
    }
}
