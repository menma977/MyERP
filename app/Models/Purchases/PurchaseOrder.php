<?php

namespace App\Models\Purchases;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\Items\GoodReceipt;
use App\Models\Items\GoodReceiptComponent;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Represents a Purchase Order in the system.
 *
 * @property string $id
 * @property string $purchase_request_id
 * @property string $purchase_procurement_id
 * @property string $code
 * @property float $request_total
 * @property float $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PurchaseOrderComponent> $components
 * @property-read int|null $component_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read PurchaseProcurement|null $procurement
 * @property-read PurchaseRequest|null $request
 * @property-read PurchaseReturn|null $return
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PurchaseOrder newModelQuery()
 * @method static Builder<static>|PurchaseOrder newQuery()
 * @method static Builder<static>|PurchaseOrder onlyTrashed()
 * @method static Builder<static>|PurchaseOrder query()
 * @method static Builder<static>|PurchaseOrder whereCode($value)
 * @method static Builder<static>|PurchaseOrder whereCreatedAt($value)
 * @method static Builder<static>|PurchaseOrder whereCreatedBy($value)
 * @method static Builder<static>|PurchaseOrder whereDeletedAt($value)
 * @method static Builder<static>|PurchaseOrder whereDeletedBy($value)
 * @method static Builder<static>|PurchaseOrder whereId($value)
 * @method static Builder<static>|PurchaseOrder whereNote($value)
 * @method static Builder<static>|PurchaseOrder wherePurchaseProcurementId($value)
 * @method static Builder<static>|PurchaseOrder wherePurchaseRequestId($value)
 * @method static Builder<static>|PurchaseOrder whereRequestTotal($value)
 * @method static Builder<static>|PurchaseOrder whereTotal($value)
 * @method static Builder<static>|PurchaseOrder whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseOrder whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseOrder withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseOrder withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseOrder extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_request_id',
        'purchase_procurement_id',
        'code',
        'request_total',
        'total',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<PurchaseRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * @return BelongsTo<PurchaseProcurement, $this>
     */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(PurchaseProcurement::class);
    }

    /**
     * @return HasOne<PurchaseReturn, $this>
     */
    public function return(): HasOne
    {
        return $this->hasOne(PurchaseReturn::class);
    }

    /**
     * @return HasMany<PurchaseOrderComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(PurchaseOrderComponent::class);
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $purchaseOrder = PurchaseOrder::find($approvalEvent->id);
                if (! $purchaseOrder) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Purchase Order']),
                    ]);
                }

                $goodsReceipt = new GoodReceipt;
                $goodsReceipt->purchase_order_id = $purchaseOrder->id;
                $goodsReceipt->code = CodeGeneratorService::code('GR')->number(GoodReceipt::count())->generate();
                $goodsReceipt->total = $purchaseOrder->total;
                $goodsReceipt->note = $purchaseOrder->note;
                $goodsReceipt->save();

                foreach ($purchaseOrder->components as $component) {
                    $goodsReceiptComponent = new GoodReceiptComponent;
                    $goodsReceiptComponent->good_receipt_id = $goodsReceipt->id;
                    $goodsReceiptComponent->purchase_order_component_id = $component->id;
                    $goodsReceiptComponent->item_id = $component->item_id;
                    $goodsReceiptComponent->quantity = $component->quantity;
                    $goodsReceiptComponent->price = $component->price;
                    $goodsReceiptComponent->total = $component->total;
                    $goodsReceiptComponent->save();
                }
            });
        }
    }
}
