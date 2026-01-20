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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Represents a Purchase Procurement in the system.
 *
 * @property string $id
 * @property string $purchase_request_id
 * @property string $code
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PurchaseProcurementComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read PurchaseRequest|null $request
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|PurchaseProcurement newModelQuery()
 * @method static Builder<static>|PurchaseProcurement newQuery()
 * @method static Builder<static>|PurchaseProcurement onlyTrashed()
 * @method static Builder<static>|PurchaseProcurement query()
 * @method static Builder<static>|PurchaseProcurement whereCode($value)
 * @method static Builder<static>|PurchaseProcurement whereCreatedAt($value)
 * @method static Builder<static>|PurchaseProcurement whereCreatedBy($value)
 * @method static Builder<static>|PurchaseProcurement whereDeletedAt($value)
 * @method static Builder<static>|PurchaseProcurement whereDeletedBy($value)
 * @method static Builder<static>|PurchaseProcurement whereId($value)
 * @method static Builder<static>|PurchaseProcurement whereNote($value)
 * @method static Builder<static>|PurchaseProcurement wherePurchaseRequestId($value)
 * @method static Builder<static>|PurchaseProcurement whereUpdatedAt($value)
 * @method static Builder<static>|PurchaseProcurement whereUpdatedBy($value)
 * @method static Builder<static>|PurchaseProcurement withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PurchaseProcurement withoutTrashed()
 *
 * @mixin Eloquent
 */
class PurchaseProcurement extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_request_id',
        'code',
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
     * @return HasMany<PurchaseProcurementComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(PurchaseProcurementComponent::class);
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $purchaseProcurement = PurchaseProcurement::find($approvalEvent->id);
                if (! $purchaseProcurement) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Purchase Procurement']),
                    ]);
                }

                $purchaseOrder = new PurchaseOrder;
                $purchaseOrder->purchase_procurement_id = $purchaseProcurement->id;
                $purchaseOrder->code = CodeGeneratorService::code('PO')->number(PurchaseOrder::count())->generate();
                $purchaseOrder->request_total = $purchaseProcurement->components->sum('total');
                $purchaseOrder->total = $purchaseOrder->request_total;
                $purchaseOrder->save();

                foreach ($purchaseProcurement->components as $component) {
                    $purchaseOrderComponent = new PurchaseOrderComponent;
                    $purchaseOrderComponent->purchase_order_id = $purchaseOrder->id;
                    $purchaseOrderComponent->purchase_procurement_component_id = $component->id;
                    $purchaseOrderComponent->item_id = $component->item_id;
                    $purchaseOrderComponent->request_quantity = $component->quantity;
                    $purchaseOrderComponent->request_price = $component->price;
                    $purchaseOrderComponent->request_total = $component->total;
                    $purchaseOrderComponent->quantity = $component->quantity;
                    $purchaseOrderComponent->price = $component->price;
                    $purchaseOrderComponent->total = $component->total;
                    $purchaseOrderComponent->note = $component->note;
                    $purchaseOrderComponent->save();
                }
            });
        }
    }
}
