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
 * @property string $vendor_invoice_id
 * @property string $vendor_component_id
 * @property string $item_id
 * @property string $quantity
 * @property string $price
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read VendorInvoice|null $invoice
 * @property-read User|null $updatedBy
 * @property-read VendorComponent $vendorComponent
 *
 * @method static Builder<static>|VendorInvoiceComponent newModelQuery()
 * @method static Builder<static>|VendorInvoiceComponent newQuery()
 * @method static Builder<static>|VendorInvoiceComponent onlyTrashed()
 * @method static Builder<static>|VendorInvoiceComponent query()
 * @method static Builder<static>|VendorInvoiceComponent whereCreatedAt($value)
 * @method static Builder<static>|VendorInvoiceComponent whereCreatedBy($value)
 * @method static Builder<static>|VendorInvoiceComponent whereDeletedAt($value)
 * @method static Builder<static>|VendorInvoiceComponent whereDeletedBy($value)
 * @method static Builder<static>|VendorInvoiceComponent whereId($value)
 * @method static Builder<static>|VendorInvoiceComponent whereItemId($value)
 * @method static Builder<static>|VendorInvoiceComponent wherePrice($value)
 * @method static Builder<static>|VendorInvoiceComponent whereQuantity($value)
 * @method static Builder<static>|VendorInvoiceComponent whereTotal($value)
 * @method static Builder<static>|VendorInvoiceComponent whereUpdatedAt($value)
 * @method static Builder<static>|VendorInvoiceComponent whereUpdatedBy($value)
 * @method static Builder<static>|VendorInvoiceComponent whereVendorComponentId($value)
 * @method static Builder<static>|VendorInvoiceComponent whereVendorInvoiceId($value)
 * @method static Builder<static>|VendorInvoiceComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorInvoiceComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorInvoiceComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_invoice_id',
        'vendor_component_id',
        'item_id',
        'quantity',
        'price',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<VendorInvoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(VendorInvoice::class);
    }

    /**
     * @return BelongsTo<VendorComponent, $this>
     */
    public function vendorComponent(): BelongsTo
    {
        return $this->belongsTo(VendorComponent::class);
    }
}
