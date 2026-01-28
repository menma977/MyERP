<?php

namespace App\Models\Sales;

use App\Abstracts\ApprovalAbstract;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Resources\Sales\SalesPaymentResource;
use App\Models\Approval\ApprovalEvent;
use App\Models\Customer\Customer;
use App\Models\Transactions\Ledger;
use App\Models\Transactions\LedgerComponent;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Represents a payment received for a sales invoice.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $sales_invoice_id
 * @property string|null $customer_id
 * @property string $code
 * @property numeric $total
 * @property PaymentMethodEnum $method
 * @property Carbon|null $paid_at
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read SalesInvoice $salesInvoice
 * @property-read User|null $createdBy
 * @property-read \App\Models\Customer\Customer|null $customer
 * @property-read User|null $updatedBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 *
 * @method static Builder<static>|SalesPayment newModelQuery()
 * @method static Builder<static>|SalesPayment newQuery()
 * @method static Builder<static>|SalesPayment onlyTrashed()
 * @method static Builder<static>|SalesPayment query()
 * @method static Builder<static>|SalesPayment withContributors()
 * @method static Builder<static>|SalesPayment withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesPayment withUsers()
 * @method static Builder<static>|SalesPayment withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(SalesPaymentResource::class)]
class SalesPayment extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'sales_invoice_id',
        'customer_id',
        'code',
        'total',
        'method',
        'paid_at',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return BelongsTo<\App\Models\Customer\Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<SalesInvoice, $this>
     */
    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'method' => PaymentMethodEnum::class,
            'paid_at' => 'datetime',
        ];
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $salesPayment = SalesPayment::lockForUpdate()->with('salesInvoice')->find($approvalEvent->requestable_id);
                if (! $salesPayment) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Sales Payment']),
                    ]);
                }

                $invoice = $salesPayment->salesInvoice;
                $invoice->paid += $salesPayment->total;

                $invoice->status = $invoice->paid >= $invoice->grand_total ? PaymentStatusEnum::PAID : PaymentStatusEnum::PARTIAL;

                $invoice->save();

                $latestLedgerTotal = Ledger::latest()->value('total') ?? 0;

                $ledger = new Ledger;
                $ledger->code = CodeGeneratorService::code('LDG-SP')->number(Ledger::count())->generate();
                $ledger->in = $salesPayment->total;
                $ledger->out = 0;
                $ledger->total = $latestLedgerTotal + $salesPayment->total;
                $ledger->save();

                $ledgerComponent = new LedgerComponent;
                $ledgerComponent->ledger_id = $ledger->id;
                $ledgerComponent->in = $salesPayment->total;
                $ledgerComponent->out = 0;
                $ledgerComponent->total = $ledger->total;
                $ledgerComponent->save();
            });
        }
    }
}
