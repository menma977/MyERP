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
 * @property string $vendor_component_id
 * @property string $price
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 * @property-read VendorComponent $vendorComponent
 *
 * @method static Builder<static>|VendorComponentHistory newModelQuery()
 * @method static Builder<static>|VendorComponentHistory newQuery()
 * @method static Builder<static>|VendorComponentHistory onlyTrashed()
 * @method static Builder<static>|VendorComponentHistory query()
 * @method static Builder<static>|VendorComponentHistory whereCreatedAt($value)
 * @method static Builder<static>|VendorComponentHistory whereCreatedBy($value)
 * @method static Builder<static>|VendorComponentHistory whereDeletedAt($value)
 * @method static Builder<static>|VendorComponentHistory whereDeletedBy($value)
 * @method static Builder<static>|VendorComponentHistory whereId($value)
 * @method static Builder<static>|VendorComponentHistory wherePrice($value)
 * @method static Builder<static>|VendorComponentHistory whereUpdatedAt($value)
 * @method static Builder<static>|VendorComponentHistory whereUpdatedBy($value)
 * @method static Builder<static>|VendorComponentHistory whereVendorComponentId($value)
 * @method static Builder<static>|VendorComponentHistory withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorComponentHistory withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorComponentHistory extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_component_id',
        'price',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<VendorComponent, $this>
     */
    public function vendorComponent(): BelongsTo
    {
        return $this->belongsTo(VendorComponent::class);
    }
}
