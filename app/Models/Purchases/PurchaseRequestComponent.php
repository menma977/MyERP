<?php

namespace App\Models\Purchases;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use App\Models\Vendors\Vendor;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $purchase_request_id
 * @property int $vendor_id
 * @property float $price
 * @property float $quantity
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
 * @property-read PurchaseRequest|null $request
 * @property-read User|null $updatedBy
 * @property-read Vendor $vendor
 *
 * @method static Builder<static>|PurchaseRequestComponent newModelQuery()
 * @method static Builder<static>|PurchaseRequestComponent newQuery()
 * @method static Builder<static>|PurchaseRequestComponent onlyTrashed()
 * @method static Builder<static>|PurchaseRequestComponent query()
 * @method static Builder<static>|PurchaseRequestComponent whereCreatedAt($value)
 * @method static Builder<static>|PurchaseRequestComponent whereCreatedBy($value)
 * @method static Builder<static>|PurchaseRequestComponent whereDeletedAt($value)
 * @method static Builder<static>|PurchaseRequestComponent whereDeletedBy($value)
 * @method static Builder<static>|PurchaseRequestComponent whereId($value)
 * @method static Builder<static>|PurchaseRequestComponent whereNote($value)
 * @method static Builder<static>|PurchaseRequestComponent wherePrice($value)
 * @method static Builder<static>|PurchaseRequestComponent wherePurchaseRequestId($value)
 * @method static Builder<static>|PurchaseRequestComponent whereQuantity($value)
 * @method static Builder<static>|PurchaseRequestComponent whereTotal($value)
 * @method static Builder<static>|PurchaseRequestComponent whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseRequestComponent whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseRequestComponent whereVendorId($value)
 * @method static Builder<static>|PurchaseRequestComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseRequestComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseRequestComponent extends ModelAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_request_id',
        'vendor_id',
        'price',
        'quantity',
        'total',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<PurchaseRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * @return BelongsTo<Vendor, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user that created the component.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that updated the component.
     *
     * @return BelongsTo<User, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user that deleted the component.
     *
     * @return BelongsTo<User, $this>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
