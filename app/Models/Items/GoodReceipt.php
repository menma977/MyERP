<?php

namespace App\Models\Items;

use App\Abstracts\ApprovalAbstract;
use App\Models\Approval\ApprovalEvent;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseReturn;
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
 * Represents a Good Receipt in the system.
 *
 * @property string $id
 * @property string $purchase_order_id
 * @property string $code
 * @property float $total
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, GoodReceiptComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read PurchaseOrder|null $order
 * @property-read Collection<int, PurchaseReturn> $purchaseReturns
 * @property-read int|null $purchase_returns_count
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|GoodReceipt newModelQuery()
 * @method static Builder<static>|GoodReceipt newQuery()
 * @method static Builder<static>|GoodReceipt onlyTrashed()
 * @method static Builder<static>|GoodReceipt query()
 * @method static Builder<static>|GoodReceipt whereCode($value)
 * @method static Builder<static>|GoodReceipt whereCreatedAt($value)
 * @method static Builder<static>|GoodReceipt whereCreatedBy($value)
 * @method static Builder<static>|GoodReceipt whereDeletedAt($value)
 * @method static Builder<static>|GoodReceipt whereDeletedBy($value)
 * @method static Builder<static>|GoodReceipt whereId($value)
 * @method static Builder<static>|GoodReceipt whereNote($value)
 * @method static Builder<static>|GoodReceipt wherePurchaseOrderId($value)
 * @method static Builder<static>|GoodReceipt whereTotal($value)
 * @method static Builder<static>|GoodReceipt whereUpdatedAt($value)
 * @method static Builder<static>|GoodReceipt whereUpdatedBy($value)
 * @method static Builder<static>|GoodReceipt withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|GoodReceipt withoutTrashed()
 *
 * @mixin Eloquent
 */
class GoodReceipt extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'purchase_order_id',
        'code',
        'total',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * Get the purchase order associated with the good issue.
     *
     * @return BelongsTo<PurchaseOrder, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the goods component associated with the good issue.
     *
     * @return HasMany<GoodReceiptComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(GoodReceiptComponent::class);
    }

    /**
     * Get the purchase returns associated with the good receipt.
     *
     * @return HasMany<PurchaseReturn, $this>
     *
     * @noinspection PhpUnused
     */
    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class, 'good_receipt_id');
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $goodReceipt = GoodReceipt::find($approvalEvent->id);
                if (! $goodReceipt) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Good Receipt']),
                    ]);
                }

                $invoice = new PurchaseInvoice;
                $invoice->purchase_order_id = $goodReceipt->purchase_order_id;
                $invoice->code = CodeGeneratorService::code('PI')->number(PurchaseInvoice::count())->generate();
                $invoice->total = $goodReceipt->total;
                $invoice->tax = PurchaseInvoice::TAX * $goodReceipt->total;
                $invoice->save();

                foreach ($goodReceipt->components as $component) {
                    $invoiceComponent = new PurchaseInvoiceComponent;
                    $invoiceComponent->purchase_invoice_id = $invoice->id;
                    $invoiceComponent->purchase_order_component_id = $component->purchase_order_component_id;
                    $invoiceComponent->item_id = $component->item_id;
                    $invoiceComponent->quantity = $component->quantity;
                    $invoiceComponent->price = $component->price;
                    $invoiceComponent->total = $component->total;
                    $invoiceComponent->save();
                }
            });
        }
    }
}
