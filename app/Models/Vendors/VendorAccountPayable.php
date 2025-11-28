<?php

namespace App\Models\Vendors;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
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
 * @property string $id
 * @property int $vendor_id
 * @property string $vendor_invoice_id
 * @property string $amount
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, VendorAccountPayableComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read User|null $updatedBy
 * @property-read Vendor $vendor
 * @property-read VendorInvoice $vendorInvoice
 *
 * @method static Builder<static>|VendorAccountPayable newModelQuery()
 * @method static Builder<static>|VendorAccountPayable newQuery()
 * @method static Builder<static>|VendorAccountPayable onlyTrashed()
 * @method static Builder<static>|VendorAccountPayable query()
 * @method static Builder<static>|VendorAccountPayable whereAmount($value)
 * @method static Builder<static>|VendorAccountPayable whereCreatedAt($value)
 * @method static Builder<static>|VendorAccountPayable whereCreatedBy($value)
 * @method static Builder<static>|VendorAccountPayable whereDeletedAt($value)
 * @method static Builder<static>|VendorAccountPayable whereDeletedBy($value)
 * @method static Builder<static>|VendorAccountPayable whereId($value)
 * @method static Builder<static>|VendorAccountPayable whereNote($value)
 * @method static Builder<static>|VendorAccountPayable whereUpdatedAt($value)
 * @method static Builder<static>|VendorAccountPayable whereUpdatedBy($value)
 * @method static Builder<static>|VendorAccountPayable whereVendorId($value)
 * @method static Builder<static>|VendorAccountPayable whereVendorInvoiceId($value)
 * @method static Builder<static>|VendorAccountPayable withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorAccountPayable withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorAccountPayable extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'vendor_invoice_id',
        'amount',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<Vendor, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @return BelongsTo<VendorInvoice, $this>
     */
    public function vendorInvoice(): BelongsTo
    {
        return $this->belongsTo(VendorInvoice::class);
    }

    /**
     * @return HasMany<VendorAccountPayableComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(VendorAccountPayableComponent::class);
    }
}
