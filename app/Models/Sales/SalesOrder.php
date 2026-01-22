<?php

namespace App\Models\Sales;

use App\Abstracts\ApprovalAbstract;
use App\Enums\DiscountTypeEnum;
use App\Models\Approval\ApprovalEvent;
use App\Models\Items\ItemBatch;
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
 * Represents a Sales Order in the system.
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
 * @property-read Collection<int, SalesOrderComponent> $components
 * @property-read int|null $components_count
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read ApprovalEvent|null $event
 * @property-read SalesInvoice|null $invoice
 * @property-read Collection<int, SalesReturn> $salesReturns
 * @property-read int|null $sales_returns_count
 * @property-read User|null $updatedBy
 *
 * @method static Builder<static>|SalesOrder newModelQuery()
 * @method static Builder<static>|SalesOrder newQuery()
 * @method static Builder<static>|SalesOrder onlyTrashed()
 * @method static Builder<static>|SalesOrder query()
 * @method static Builder<static>|SalesOrder whereCode($value)
 * @method static Builder<static>|SalesOrder whereCreatedAt($value)
 * @method static Builder<static>|SalesOrder whereCreatedBy($value)
 * @method static Builder<static>|SalesOrder whereDeletedAt($value)
 * @method static Builder<static>|SalesOrder whereDeletedBy($value)
 * @method static Builder<static>|SalesOrder whereId($value)
 * @method static Builder<static>|SalesOrder whereTotal($value)
 * @method static Builder<static>|SalesOrder whereUpdatedAt($value)
 * @method static Builder<static>|SalesOrder whereUpdatedBy($value)
 * @method static Builder<static>|SalesOrder withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|SalesOrder withoutTrashed()
 *
 * @mixin Eloquent
 */
class SalesOrder extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
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
     * @return HasMany<SalesOrderComponent, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(SalesOrderComponent::class);
    }

    /**
     * @return HasOne<SalesInvoice, $this>
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(SalesInvoice::class);
    }

    /**
     * Get the sales returns associated with the sales order.
     *
     * @return HasMany<SalesReturn, $this>
     */
    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class, 'sales_order_id');
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $salesOrder = SalesOrder::find($approvalEvent->id);
                if (! $salesOrder) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Sales Order']),
                    ]);
                }

                $salesInvoice = new SalesInvoice;
                $salesInvoice->sales_order_id = $salesOrder->id;
                $salesInvoice->code = CodeGeneratorService::code('SI')->number(SalesInvoice::count())->generate();
                $salesInvoice->total = $salesOrder->total;
                $salesInvoice->discount_type = DiscountTypeEnum::AMOUNT;
                $salesInvoice->discount = 0;
                $salesInvoice->grand_total = 0;
                $salesInvoice->save();

                foreach ($salesOrder->components as $component) {
                    $remaining = $component->quantity;
                    $batches = ItemBatch::with([
                        'stock',
                    ])->where('item_id', $component->item_id)->where('is_available', true)->orderBy('expired_at')->get();

                    foreach ($batches as $batch) {
                        if ($remaining <= 0) {
                            break;
                        }

                        $stock = $batch->stock;
                        if (! $stock || $stock->quantity <= 0) {
                            continue;
                        }

                        $quantityToTake = min($remaining, $stock->quantity);

                        $salesInvoiceComponent = new SalesInvoiceComponent;
                        $salesInvoiceComponent->sales_invoice_id = $salesInvoice->id;
                        $salesInvoiceComponent->item_id = $component->item_id;
                        $salesInvoiceComponent->item_batch_id = $batch->id;
                        $salesInvoiceComponent->item_stock_id = $stock->id;
                        $salesInvoiceComponent->quantity = $quantityToTake;
                        $salesInvoiceComponent->price = $component->price;
                        $salesInvoiceComponent->total = $salesInvoiceComponent->quantity * $salesInvoiceComponent->price;
                        $salesInvoiceComponent->save();

                        $remaining -= $quantityToTake;
                    }
                }
            });
        }
    }
}
