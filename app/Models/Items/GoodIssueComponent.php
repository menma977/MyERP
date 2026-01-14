<?php

namespace App\Models\Items;

use App\Abstracts\ModelAbstract;
use App\Models\Sales\SalesInvoiceComponent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $good_issue_id
 * @property string $sales_invoice_component_id
 * @property string $item_id
 * @property string $item_batch_id
 * @property string $item_stock_id
 * @property string $quantity
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ItemBatch|null $batch
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read GoodIssue|null $good
 * @property-read Item $item
 * @property-read SalesInvoiceComponent $salesInvoiceComponent
 * @property-read ItemStock|null $stock
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|GoodIssueComponent newModelQuery()
 * @method static Builder<static>|GoodIssueComponent newQuery()
 * @method static Builder<static>|GoodIssueComponent onlyTrashed()
 * @method static Builder<static>|GoodIssueComponent query()
 * @method static Builder<static>|GoodIssueComponent whereCreatedAt($value)
 * @method static Builder<static>|GoodIssueComponent whereCreatedBy($value)
 * @method static Builder<static>|GoodIssueComponent whereDeletedAt($value)
 * @method static Builder<static>|GoodIssueComponent whereDeletedBy($value)
 * @method static Builder<static>|GoodIssueComponent whereGoodIssueId($value)
 * @method static Builder<static>|GoodIssueComponent whereId($value)
 * @method static Builder<static>|GoodIssueComponent whereItemBatchId($value)
 * @method static Builder<static>|GoodIssueComponent whereItemId($value)
 * @method static Builder<static>|GoodIssueComponent whereItemStockId($value)
 * @method static Builder<static>|GoodIssueComponent whereQuantity($value)
 * @method static Builder<static>|GoodIssueComponent whereSalesInvoiceComponentId($value)
 * @method static Builder<static>|GoodIssueComponent whereUpdatedAt($value)
 * @method static Builder<static>|GoodIssueComponent whereUpdatedBy($value)
 * @method static Builder<static>|GoodIssueComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|GoodIssueComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class GoodIssueComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'good_issue_id',
        'sales_invoice_component_id',
        'item_id',
        'item_batch_id',
        'item_stock_id',
        'quantity',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * Get the good issue associated with the component.
     *
     * @return BelongsTo<GoodIssue, $this>
     */
    public function good(): BelongsTo
    {
        return $this->belongsTo(GoodIssue::class);
    }

    /**
     * Get the sales invoice component associated with the good issue component.
     *
     * @return BelongsTo<SalesInvoiceComponent, $this>
     */
    public function salesInvoiceComponent(): BelongsTo
    {
        return $this->belongsTo(SalesInvoiceComponent::class);
    }

    /**
     * Get the item associated with the good issue component.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the item batch associated with the good issue component.
     *
     * @return BelongsTo<ItemBatch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ItemBatch::class);
    }

    /**
     * Get the item stock associated with the good issue component.
     *
     * @return BelongsTo<ItemStock, $this>
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(ItemStock::class);
    }
}
