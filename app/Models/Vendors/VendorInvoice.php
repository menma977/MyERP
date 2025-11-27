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
 * @property string $code
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, VendorInvoiceComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read User|null $updatedBy
 * @property-read Vendor $vendor
 *
 * @method static Builder<static>|VendorInvoice newModelQuery()
 * @method static Builder<static>|VendorInvoice newQuery()
 * @method static Builder<static>|VendorInvoice onlyTrashed()
 * @method static Builder<static>|VendorInvoice query()
 * @method static Builder<static>|VendorInvoice whereCode($value)
 * @method static Builder<static>|VendorInvoice whereCreatedAt($value)
 * @method static Builder<static>|VendorInvoice whereCreatedBy($value)
 * @method static Builder<static>|VendorInvoice whereDeletedAt($value)
 * @method static Builder<static>|VendorInvoice whereDeletedBy($value)
 * @method static Builder<static>|VendorInvoice whereId($value)
 * @method static Builder<static>|VendorInvoice whereTotal($value)
 * @method static Builder<static>|VendorInvoice whereUpdatedAt($value)
 * @method static Builder<static>|VendorInvoice whereUpdatedBy($value)
 * @method static Builder<static>|VendorInvoice whereVendorId($value)
 * @method static Builder<static>|VendorInvoice withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorInvoice withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorInvoice extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'code',
        'total',
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
     * @return HasMany<VendorInvoiceComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(VendorInvoiceComponent::class);
    }
}
