<?php

namespace App\Models\Items;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\Sales\SalesInvoice;
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
 * Represents a Good Issue in the system.
 *
 * @property string $id
 * @property string $sales_invoice_id
 * @property string $code
 * @property string $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, GoodIssueComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read SalesInvoice $salesInvoice
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|GoodIssue newModelQuery()
 * @method static Builder<static>|GoodIssue newQuery()
 * @method static Builder<static>|GoodIssue onlyTrashed()
 * @method static Builder<static>|GoodIssue query()
 * @method static Builder<static>|GoodIssue whereCode($value)
 * @method static Builder<static>|GoodIssue whereCreatedAt($value)
 * @method static Builder<static>|GoodIssue whereCreatedBy($value)
 * @method static Builder<static>|GoodIssue whereDeletedAt($value)
 * @method static Builder<static>|GoodIssue whereDeletedBy($value)
 * @method static Builder<static>|GoodIssue whereId($value)
 * @method static Builder<static>|GoodIssue whereNote($value)
 * @method static Builder<static>|GoodIssue whereSalesInvoiceId($value)
 * @method static Builder<static>|GoodIssue whereTotal($value)
 * @method static Builder<static>|GoodIssue whereUpdatedAt($value)
 * @method static Builder<static>|GoodIssue whereUpdatedBy($value)
 * @method static Builder<static>|GoodIssue withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|GoodIssue withoutTrashed()
 *
 * @mixin Eloquent
 */
class GoodIssue extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sales_invoice_id',
        'code',
        'total',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * Get the sales invoice associated with the good issue.
     *
     * @return BelongsTo<SalesInvoice, $this>
     */
    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    /**
     * Get the components associated with the good issue.
     *
     * @return HasMany<GoodIssueComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(GoodIssueComponent::class);
    }
}
