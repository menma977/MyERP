<?php

namespace App\Models\Sales;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\Items\GoodIssue;
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
 * Represents a Sales Invoice in the system.
 *
 * @property string $id
 * @property string $sales_order_id
 * @property string $code
 * @property string $total
 * @property string $tax
 * @property string $discount_type
 * @property string $discount
 * @property string $fee
 * @property string $grand_total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, SalesInvoiceComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read Collection<int, GoodIssue> $goodIssues
 * @property-read int|null $good_issues_count
 * @property-read SalesOrder|null $order
 * @property-read Collection<int, SalesReturn> $salesReturns
 * @property-read int|null $sales_returns_count
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|SalesInvoice newModelQuery()
 * @method static Builder<static>|SalesInvoice newQuery()
 * @method static Builder<static>|SalesInvoice onlyTrashed()
 * @method static Builder<static>|SalesInvoice query()
 * @method static Builder<static>|SalesInvoice whereCode($value)
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
 * @method static Builder<static>|SalesInvoice withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesInvoice withoutTrashed()
 *
 * @mixin Eloquent
 */
class SalesInvoice extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sales_order_id',
        'code',
        'total',
        'tax',
        'discount_type',
        'discount',
        'fee',
        'grand_total',
        'note',
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
}
