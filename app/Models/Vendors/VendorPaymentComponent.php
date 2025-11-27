<?php

namespace App\Models\Vendors;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $vendor_payment_id
 * @property string $vendor_account_payable_component_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read VendorAccountPayableComponent|null $accountPayableComponent
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read VendorPayment|null $payment
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|VendorPaymentComponent newModelQuery()
 * @method static Builder<static>|VendorPaymentComponent newQuery()
 * @method static Builder<static>|VendorPaymentComponent onlyTrashed()
 * @method static Builder<static>|VendorPaymentComponent query()
 * @method static Builder<static>|VendorPaymentComponent whereCreatedAt($value)
 * @method static Builder<static>|VendorPaymentComponent whereCreatedBy($value)
 * @method static Builder<static>|VendorPaymentComponent whereDeletedAt($value)
 * @method static Builder<static>|VendorPaymentComponent whereDeletedBy($value)
 * @method static Builder<static>|VendorPaymentComponent whereId($value)
 * @method static Builder<static>|VendorPaymentComponent wherePrice($value)
 * @method static Builder<static>|VendorPaymentComponent whereQuantity($value)
 * @method static Builder<static>|VendorPaymentComponent whereTotal($value)
 * @method static Builder<static>|VendorPaymentComponent whereUpdatedAt($value)
 * @method static Builder<static>|VendorPaymentComponent whereUpdatedBy($value)
 * @method static Builder<static>|VendorPaymentComponent whereVendorAccountPayableComponentId($value)
 * @method static Builder<static>|VendorPaymentComponent whereVendorPaymentId($value)
 * @method static Builder<static>|VendorPaymentComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorPaymentComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorPaymentComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_payment_id',
        'vendor_account_payable_component_id',
        'quantity',
        'price',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<VendorPayment, $this>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(VendorPayment::class);
    }

    /**
     * @return BelongsTo<VendorAccountPayableComponent, $this>
     */
    public function accountPayableComponent(): BelongsTo
    {
        return $this->belongsTo(VendorAccountPayableComponent::class);
    }
}
