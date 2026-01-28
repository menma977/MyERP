<?php

namespace App\Models\Sales;

use App\Abstracts\ApprovalAbstract;
use App\Enums\DiscountTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Resources\Sales\SalesInvoiceResource;
use App\Models\Approval\ApprovalEvent;
use App\Models\Customer\Customer;
use App\Models\Items\GoodIssue;
use App\Models\Transactions\Ledger;
use App\Models\Transactions\LedgerComponent;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Database\Factories\Sales\SalesInvoiceFactory;
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
 * Represents a Sales Invoice in the system.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $sales_order_id
 * @property string|null $customer_id
 * @property string $code
 * @property numeric $total
 * @property numeric $tax
 * @property DiscountTypeEnum $discount_type
 * @property numeric $discount
 * @property numeric $fee
 * @property numeric $grand_total
 * @property numeric $paid
 * @property string|null $note
 * @property PaymentMethodEnum $method
 * @property PaymentStatusEnum $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\Sales\SalesInvoiceComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read \App\Models\Customer\Customer|null $customer
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read Collection<int, GoodIssue> $goodIssues
 * @property-read int|null $good_issues_count
 * @property-read \App\Models\Sales\SalesOrder|null $order
 * @property-read Collection<int, \App\Models\Sales\SalesReturn> $salesReturns
 * @property-read int|null $sales_returns_count
 * @property-read User|null $updatedBy
 *
 * @method static SalesInvoiceFactory factory($count = null, $state = [])
 * @method static Builder<static>|SalesInvoice newModelQuery()
 * @method static Builder<static>|SalesInvoice newQuery()
 * @method static Builder<static>|SalesInvoice onlyTrashed()
 * @method static Builder<static>|SalesInvoice query()
 * @method static Builder<static>|SalesInvoice whereCode($value)
 * @method static Builder<static>|SalesInvoice whereCompanyId($value)
 * @method static Builder<static>|SalesInvoice whereCreatedAt($value)
 * @method static Builder<static>|SalesInvoice whereCreatedBy($value)
 * @method static Builder<static>|SalesInvoice whereDeletedAt($value)
 * @method static Builder<static>|SalesInvoice whereDeletedBy($value)
 * @method static Builder<static>|SalesInvoice whereDiscount($value)
 * @method static Builder<static>|SalesInvoice whereDiscountType($value)
 * @method static Builder<static>|SalesInvoice whereFee($value)
 * @method static Builder<static>|SalesInvoice whereGrandTotal($value)
 * @method static Builder<static>|SalesInvoice whereId($value)
 * @method static Builder<static>|SalesInvoice whereNote($value)
 * @method static Builder<static>|SalesInvoice whereSalesOrderId($value)
 * @method static Builder<static>|SalesInvoice whereTax($value)
 * @method static Builder<static>|SalesInvoice whereTotal($value)
 * @method static Builder<static>|SalesInvoice whereUpdatedAt($value)
 * @method static Builder<static>|SalesInvoice whereUpdatedBy($value)
 * @method static Builder<static>|SalesInvoice withContributors()
 * @method static Builder<static>|SalesInvoice withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesInvoice withUsers()
 * @method static Builder<static>|SalesInvoice withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(SalesInvoiceResource::class)]
class SalesInvoice extends ApprovalAbstract
{
    /** @use HasFactory<\Database\Factories\Sales\SalesInvoiceFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'sales_order_id',
        'customer_id',
        'code',
        'total',
        'tax',
        'discount_type',
        'discount',
        'fee',
        'grand_total',
        'paid',
        'note',
        'method',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<\App\Models\Customer\Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<SalesOrder, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * @return HasMany<SalesInvoiceComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(SalesInvoiceComponent::class, 'sales_invoice_id');
    }

    /**
     * Get the good issues associated with the sales invoice.
     *
     * @return HasMany<GoodIssue, $this>
     */
    public function goodIssues(): HasMany
    {
        return $this->hasMany(GoodIssue::class, 'sales_invoice_id');
    }

    /**
     * Get the sales returns associated with the sales invoice.
     *
     * @return HasMany<SalesReturn, $this>
     */
    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class, 'sales_invoice_id');
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount_type' => DiscountTypeEnum::class,
            'discount' => 'decimal:2',
            'fee' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid' => 'decimal:2',
            'method' => PaymentMethodEnum::class,
            'status' => PaymentStatusEnum::class,
        ];
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $salesInvoice = SalesInvoice::lockForUpdate()->with('components')->find($approvalEvent->requestable_id);
                if (! $salesInvoice) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Sales Invoice']),
                    ]);
                }

                if ($salesInvoice->paid < $salesInvoice->grand_total) {
                    $salesInvoice->status = PaymentStatusEnum::PARTIAL;
                } else {
                    $salesInvoice->status = PaymentStatusEnum::PAID;
                }
                $salesInvoice->save();

                if ($salesInvoice->paid > 0) {
                    $latestLedgerTotal = Ledger::latest()->value('total') ?? 0;

                    $ledger = new Ledger;
                    $ledger->code = CodeGeneratorService::code('LDG')->number(Ledger::count())->generate();
                    $ledger->in = $salesInvoice->paid;
                    $ledger->out = 0.0;
                    $ledger->total = $latestLedgerTotal + $salesInvoice->paid;
                    $ledger->save();

                    $ledgerComponent = new LedgerComponent;
                    $ledgerComponent->ledger_id = $ledger->id;
                    $ledgerComponent->in = $salesInvoice->paid;
                    $ledgerComponent->out = 0.0;
                    $ledgerComponent->total = $ledger->total;
                    $ledgerComponent->save();
                }
            });
        }
    }
}
