<?php

namespace App\Models\Vendors;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property int $vendor_id
 * @property string $vendor_account_payable_id
 * @property string $amount
 * @property string $method
 * @property string|null $note
 * @property string|null $paid_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read VendorAccountPayable|null $accountPayable
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read User|null $updatedBy
 * @property-read Vendor $vendor
 *
 * @method static Builder<static>|VendorPayment newModelQuery()
 * @method static Builder<static>|VendorPayment newQuery()
 * @method static Builder<static>|VendorPayment onlyTrashed()
 * @method static Builder<static>|VendorPayment query()
 * @method static Builder<static>|VendorPayment whereAmount($value)
 * @method static Builder<static>|VendorPayment whereCreatedAt($value)
 * @method static Builder<static>|VendorPayment whereCreatedBy($value)
 * @method static Builder<static>|VendorPayment whereDeletedAt($value)
 * @method static Builder<static>|VendorPayment whereDeletedBy($value)
 * @method static Builder<static>|VendorPayment whereId($value)
 * @method static Builder<static>|VendorPayment whereMethod($value)
 * @method static Builder<static>|VendorPayment whereNote($value)
 * @method static Builder<static>|VendorPayment wherePaidAt($value)
 * @method static Builder<static>|VendorPayment whereUpdatedAt($value)
 * @method static Builder<static>|VendorPayment whereUpdatedBy($value)
 * @method static Builder<static>|VendorPayment whereVendorAccountPayableId($value)
 * @method static Builder<static>|VendorPayment whereVendorId($value)
 * @method static Builder<static>|VendorPayment withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VendorPayment withoutTrashed()
 *
 * @mixin Eloquent
 */
class VendorPayment extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'vendor_account_payable_id',
        'amount',
        'method',
        'note',
        'paid_at',
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
     * @return BelongsTo<VendorAccountPayable, $this>
     */
    public function accountPayable(): BelongsTo
    {
        return $this->belongsTo(VendorAccountPayable::class);
    }
}
