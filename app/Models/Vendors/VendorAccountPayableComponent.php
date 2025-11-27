<?php

namespace App\Models\Vendors;

use App\Abstracts\ModelAbstract;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $vendor_account_payable_id
 * @property string $purchase_invoice_component_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read VendorAccountPayable|null $accountPayable
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read PurchaseInvoiceComponent $purchaseInvoiceComponent
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|VendorAccountPayableComponent newModelQuery()
 * @method static Builder<static>|VendorAccountPayableComponent newQuery()
 * @method static Builder<static>|VendorAccountPayableComponent onlyTrashed()
 * @method static Builder<static>|VendorAccountPayableComponent query()
 * @method static Builder<static>|VendorAccountPayableComponent whereCreatedAt($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereCreatedBy($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereDeletedAt($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereDeletedBy($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereId($value)
 * @method static Builder<static>|VendorAccountPayableComponent wherePrice($value)
 * @method static Builder<static>|VendorAccountPayableComponent wherePurchaseInvoiceComponentId($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereQuantity($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereTotal($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereUpdatedAt($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereUpdatedBy($value)
 * @method static Builder<static>|VendorAccountPayableComponent whereVendorAccountPayableId($value)
 * @method static Builder<static>|VendorAccountPayableComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorAccountPayableComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorAccountPayableComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_account_payable_id',
        'purchase_invoice_component_id',
        'quantity',
        'price',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<VendorAccountPayable, $this>
     */
    public function accountPayable(): BelongsTo
    {
        return $this->belongsTo(VendorAccountPayable::class);
    }

    /**
     * @return BelongsTo<PurchaseInvoiceComponent, $this>
     */
    public function purchaseInvoiceComponent(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoiceComponent::class);
    }
}
