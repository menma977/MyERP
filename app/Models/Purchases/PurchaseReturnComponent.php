<?php

namespace App\Models\Purchases;

use App\Abstracts\ModelAbstract;
use App\Models\Items\GoodReceiptComponent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $purchase_return_id
 * @property string $purchase_order_component_id
 * @property string $good_receipt_component_id
 * @property string $item_id
 * @property float $quantity
 * @property float $price
 * @property float $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read GoodReceiptComponent $goodReceiptComponent
 * @property-read PurchaseOrderComponent $purchaseOrderComponent
 * @property-read PurchaseReturn|null $return
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PurchaseReturnComponent newModelQuery()
 * @method static Builder<static>|PurchaseReturnComponent newQuery()
 * @method static Builder<static>|PurchaseReturnComponent onlyTrashed()
 * @method static Builder<static>|PurchaseReturnComponent query()
 * @method static Builder<static>|PurchaseReturnComponent whereCreatedAt($value)
 * @method static Builder<static>|PurchaseReturnComponent whereCreatedBy($value)
 * @method static Builder<static>|PurchaseReturnComponent whereDeletedAt($value)
 * @method static Builder<static>|PurchaseReturnComponent whereDeletedBy($value)
 * @method static Builder<static>|PurchaseReturnComponent whereGoodReceiptComponentId($value)
 * @method static Builder<static>|PurchaseReturnComponent whereId($value)
 * @method static Builder<static>|PurchaseReturnComponent whereItemId($value)
 * @method static Builder<static>|PurchaseReturnComponent whereNote($value)
 * @method static Builder<static>|PurchaseReturnComponent wherePrice($value)
 * @method static Builder<static>|PurchaseReturnComponent wherePurchaseOrderComponentId($value)
 * @method static Builder<static>|PurchaseReturnComponent wherePurchaseReturnId($value)
 * @method static Builder<static>|PurchaseReturnComponent whereQuantity($value)
 * @method static Builder<static>|PurchaseReturnComponent whereTotal($value)
 * @method static Builder<static>|PurchaseReturnComponent whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseReturnComponent whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseReturnComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseReturnComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseReturnComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_return_id',
        'purchase_order_component_id',
        'good_receipt_component_id',
        'item_id',
        'quantity',
        'price',
        'total',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<PurchaseReturn, $this>
     */
    public function return(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    /**
     * @return BelongsTo<PurchaseOrderComponent, $this>
     */
    public function purchaseOrderComponent(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderComponent::class);
    }

    /**
     * @return BelongsTo<GoodReceiptComponent, $this>
     */
    public function goodReceiptComponent(): BelongsTo
    {
        return $this->belongsTo(GoodReceiptComponent::class);
    }
}
