<?php

namespace App\Models\Purchases;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\Items\GoodReceipt;
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
 * Represents a Purchase Return in the system.
 *
 * @property string $id
 * @property string $purchase_order_id
 * @property string $good_receipt_id
 * @property string $code
 * @property string $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PurchaseReturnComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read GoodReceipt $goodReceipt
 * @property-read PurchaseOrder|null $order
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PurchaseReturn newModelQuery()
 * @method static Builder<static>|PurchaseReturn newQuery()
 * @method static Builder<static>|PurchaseReturn onlyTrashed()
 * @method static Builder<static>|PurchaseReturn query()
 * @method static Builder<static>|PurchaseReturn whereCode($value)
 * @method static Builder<static>|PurchaseReturn whereCreatedAt($value)
 * @method static Builder<static>|PurchaseReturn whereCreatedBy($value)
 * @method static Builder<static>|PurchaseReturn whereDeletedAt($value)
 * @method static Builder<static>|PurchaseReturn whereDeletedBy($value)
 * @method static Builder<static>|PurchaseReturn whereGoodReceiptId($value)
 * @method static Builder<static>|PurchaseReturn whereId($value)
 * @method static Builder<static>|PurchaseReturn whereNote($value)
 * @method static Builder<static>|PurchaseReturn wherePurchaseOrderId($value)
 * @method static Builder<static>|PurchaseReturn whereTotal($value)
 * @method static Builder<static>|PurchaseReturn whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseReturn whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseReturn withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseReturn withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseReturn extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_order_id',
        'good_receipt_id',
        'code',
        'total',
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
     * @return BelongsTo<GoodReceipt, $this>
     */
    public function goodReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodReceipt::class);
    }

    /**
     * @return HasMany<PurchaseReturnComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(PurchaseReturnComponent::class);
    }
}
