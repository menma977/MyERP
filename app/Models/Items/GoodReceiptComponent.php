<?php

namespace App\Models\Items;

use App\Abstracts\ModelWithCompanyAbstract;
use App\Http\Resources\Items\GoodReceiptComponentResource;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $purchase_order_component_id
 * @property string $good_receipt_id
 * @property string $item_id
 * @property numeric $quantity
 * @property numeric $price
 * @property numeric $total
 * @property Carbon|null $expired_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Items\GoodReceipt $goodReceipt
 * @property-read \App\Models\Items\Item $item
 * @property-read PurchaseOrderComponent $purchaseOrderComponent
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|GoodReceiptComponent newModelQuery()
 * @method static Builder<static>|GoodReceiptComponent newQuery()
 * @method static Builder<static>|GoodReceiptComponent onlyTrashed()
 * @method static Builder<static>|GoodReceiptComponent query()
 * @method static Builder<static>|GoodReceiptComponent whereCreatedAt($value)
 * @method static Builder<static>|GoodReceiptComponent whereCreatedBy($value)
 * @method static Builder<static>|GoodReceiptComponent whereDeletedAt($value)
 * @method static Builder<static>|GoodReceiptComponent whereDeletedBy($value)
 * @method static Builder<static>|GoodReceiptComponent whereExpiredAt($value)
 * @method static Builder<static>|GoodReceiptComponent whereGoodReceiptId($value)
 * @method static Builder<static>|GoodReceiptComponent whereId($value)
 * @method static Builder<static>|GoodReceiptComponent whereItemId($value)
 * @method static Builder<static>|GoodReceiptComponent wherePrice($value)
 * @method static Builder<static>|GoodReceiptComponent wherePurchaseOrderComponentId($value)
 * @method static Builder<static>|GoodReceiptComponent whereQuantity($value)
 * @method static Builder<static>|GoodReceiptComponent whereTotal($value)
 * @method static Builder<static>|GoodReceiptComponent whereUpdatedAt($value)
 * @method static Builder<static>|GoodReceiptComponent whereUpdatedBy($value)
 * @method static Builder<static>|GoodReceiptComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|GoodReceiptComponent withUsers()
 * @method static Builder<static>|GoodReceiptComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(GoodReceiptComponentResource::class)]
class GoodReceiptComponent extends ModelWithCompanyAbstract
{
    /** @use HasFactory<\Database\Factories\Items\GoodReceiptComponentFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'purchase_order_component_id',
        'good_receipt_id',
        'item_id',
        'quantity',
        'price',
        'total',
        'expired_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<PurchaseOrderComponent, $this>
     */
    public function purchaseOrderComponent(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderComponent::class);
    }

    /**
     * @return BelongsTo<GoodReceipt, $this>
     */
    public function goodReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodReceipt::class);
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }
}
