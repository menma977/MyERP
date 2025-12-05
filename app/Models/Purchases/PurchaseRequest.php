<?php

namespace App\Models\Purchases;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Represents a Purchase Request in the system.
 *
 * @property string $id
 * @property string $code
 * @property float $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PurchaseRequestComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read PurchaseOrder|null $order
 * @property-read PurchaseProcurement|null $procurement
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PurchaseRequest newModelQuery()
 * @method static Builder<static>|PurchaseRequest newQuery()
 * @method static Builder<static>|PurchaseRequest onlyTrashed()
 * @method static Builder<static>|PurchaseRequest query()
 * @method static Builder<static>|PurchaseRequest whereCode($value)
 * @method static Builder<static>|PurchaseRequest whereCreatedAt($value)
 * @method static Builder<static>|PurchaseRequest whereCreatedBy($value)
 * @method static Builder<static>|PurchaseRequest whereDeletedAt($value)
 * @method static Builder<static>|PurchaseRequest whereDeletedBy($value)
 * @method static Builder<static>|PurchaseRequest whereId($value)
 * @method static Builder<static>|PurchaseRequest whereTotal($value)
 * @method static Builder<static>|PurchaseRequest whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseRequest whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseRequest withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseRequest withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseRequest extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'total',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return HasOne<PurchaseOrder, $this>
     */
    public function order(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    /**
     * @return HasOne<PurchaseProcurement, $this>
     */
    public function procurement(): HasOne
    {
        return $this->hasOne(PurchaseProcurement::class);
    }

    /**
     * @return HasMany<PurchaseRequestComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(PurchaseRequestComponent::class);
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $purchaseRequest = PurchaseRequest::find($approvalEvent->id);
                if (!$purchaseRequest) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'purchase_request' => trans('messages.fail.approve', ['target' => 'Purchase Request']),
                    ]);
                }

                $purchaseProcurement = new PurchaseProcurement;
                $purchaseProcurement->purchase_request_id = $purchaseRequest->id;
                $purchaseProcurement->code = CodeGeneratorService::code('PPR')->number(PurchaseProcurement::count())->generate();
                $purchaseProcurement->save();

                foreach ($purchaseRequest->components as $component) {
                    $purchaseProcurementComponent = new PurchaseProcurementComponent;
                    $purchaseProcurementComponent->purchase_procurement_id = $purchaseProcurement->id;
                    $purchaseProcurementComponent->vendor_id = $component->vendor_id;
                    $purchaseProcurementComponent->price = $component->price;
                    $purchaseProcurementComponent->quantity = $component->quantity;
                    $purchaseProcurementComponent->total = $component->total;
                    $purchaseProcurementComponent->note = $component->note;
                    $purchaseProcurementComponent->save();
                }
            });
        }
    }
}
