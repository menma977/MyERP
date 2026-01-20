<?php

namespace App\Models\Vendors;

use App\Abstracts\ModelAbstract;
use App\Models\Purchases\PurchaseProcurementComponent;
use App\Models\Purchases\PurchaseRequestComponent;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, VendorComponent> $components
 * @property-read int|null $components_count
 * @property-read Collection<int, PurchaseProcurementComponent> $purchaseProcurementComponents
 * @property-read int|null $purchase_procurement_components_count
 * @property-read Collection<int, PurchaseRequestComponent> $purchaseRequestComponents
 * @property-read int|null $purchase_request_components_count
 * @property-read Collection<int, VendorAccountPayable> $vendorAccountPayables
 * @property-read int|null $vendor_account_payables_count
 * @property-read Collection<int, VendorInvoice> $vendorInvoices
 * @property-read int|null $vendor_invoices_count
 * @property-read Collection<int, VendorPayment> $vendorPayments
 * @property-read int|null $vendor_payments_count
 *
 * @method static Builder<static>|Vendor newModelQuery()
 * @method static Builder<static>|Vendor newQuery()
 * @method static Builder<static>|Vendor query()
 * @method static Builder<static>|Vendor whereAddress($value)
 * @method static Builder<static>|Vendor whereCode($value)
 * @method static Builder<static>|Vendor whereCreatedAt($value)
 * @method static Builder<static>|Vendor whereCreatedBy($value)
 * @method static Builder<static>|Vendor whereDeletedAt($value)
 * @method static Builder<static>|Vendor whereDeletedBy($value)
 * @method static Builder<static>|Vendor whereEmail($value)
 * @method static Builder<static>|Vendor whereId($value)
 * @method static Builder<static>|Vendor whereName($value)
 * @method static Builder<static>|Vendor wherePhone($value)
 * @method static Builder<static>|Vendor whereUpdatedAt($value)
 * @method static Builder<static>|Vendor whereUpdatedBy($value)
 *
 * @mixin Eloquent
 */
class Vendor extends ModelAbstract
{
    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return HasMany<PurchaseProcurementComponent, $this>
     */
    public function purchaseProcurementComponents(): HasMany
    {
        return $this->hasMany(PurchaseProcurementComponent::class);
    }

    /**
     * @return HasMany<PurchaseRequestComponent, $this>
     */
    public function purchaseRequestComponents(): HasMany
    {
        return $this->hasMany(PurchaseRequestComponent::class);
    }

    /**
     * @return HasMany<VendorAccountPayable, $this>
     */
    public function vendorAccountPayables(): HasMany
    {
        return $this->hasMany(VendorAccountPayable::class);
    }

    /**
     * @return HasMany<VendorComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(VendorComponent::class);
    }

    /**
     * @return HasMany<VendorInvoice, $this>
     */
    public function vendorInvoices(): HasMany
    {
        return $this->hasMany(VendorInvoice::class);
    }

    /**
     * @return HasMany<VendorPayment, $this>
     */
    public function vendorPayments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }
}
