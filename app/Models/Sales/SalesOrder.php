<?php

namespace App\Models\Sales;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents a Sales Order in the system.
 *
 * @property string $id
 * @property string $code
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, SalesOrderComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read SalesInvoice|null $invoice
 * @property-read Collection<int, SalesReturn> $salesReturns
 * @property-read int|null $sales_returns_count
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|SalesOrder newModelQuery()
 * @method static Builder<static>|SalesOrder newQuery()
 * @method static Builder<static>|SalesOrder onlyTrashed()
 * @method static Builder<static>|SalesOrder query()
 * @method static Builder<static>|SalesOrder whereCode($value)
 * @method static Builder<static>|SalesOrder whereCreatedAt($value)
 * @method static Builder<static>|SalesOrder whereCreatedBy($value)
 * @method static Builder<static>|SalesOrder whereDeletedAt($value)
 * @method static Builder<static>|SalesOrder whereDeletedBy($value)
 * @method static Builder<static>|SalesOrder whereId($value)
 * @method static Builder<static>|SalesOrder whereTotal($value)
 * @method static Builder<static>|SalesOrder whereUpdatedAt($value)
 * @method static Builder<static>|SalesOrder whereUpdatedBy($value)
 * @method static Builder<static>|SalesOrder withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesOrder withoutTrashed()
 *
 * @mixin Eloquent
 */
class SalesOrder extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return HasMany<SalesOrderComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(SalesOrderComponent::class);
    }

    /**
     * @return HasOne<SalesInvoice, $this>
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(SalesInvoice::class);
    }

    /**
     * Get the sales returns associated with the sales order.
     *
     * @return HasMany<SalesReturn, $this>
     */
    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class, 'sales_order_id');
    }
}
