<?php

namespace App\Models\Items;

use App\Abstracts\ModelWithCompanyAbstract;
use App\Http\Resources\Items\ItemBillComponentResource;
use App\Models\User;
use Database\Factories\Items\ItemBillComponentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents an Item Bill Component in the system.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $item_bill_id
 * @property string $item_id
 * @property numeric $quantity
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Models\Items\ItemBill $bill
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Items\Item $item
 * @property-read User|null $updatedBy
 *
 * @method static ItemBillComponentFactory factory($count = null, $state = [])
 * @method static Builder<static>|ItemBillComponent newModelQuery()
 * @method static Builder<static>|ItemBillComponent newQuery()
 * @method static Builder<static>|ItemBillComponent onlyTrashed()
 * @method static Builder<static>|ItemBillComponent query()
 * @method static Builder<static>|ItemBillComponent whereCompanyId($value)
 * @method static Builder<static>|ItemBillComponent whereCreatedAt($value)
 * @method static Builder<static>|ItemBillComponent whereCreatedBy($value)
 * @method static Builder<static>|ItemBillComponent whereDeletedAt($value)
 * @method static Builder<static>|ItemBillComponent whereDeletedBy($value)
 * @method static Builder<static>|ItemBillComponent whereId($value)
 * @method static Builder<static>|ItemBillComponent whereItemBillId($value)
 * @method static Builder<static>|ItemBillComponent whereItemId($value)
 * @method static Builder<static>|ItemBillComponent whereQuantity($value)
 * @method static Builder<static>|ItemBillComponent whereUpdatedAt($value)
 * @method static Builder<static>|ItemBillComponent whereUpdatedBy($value)
 * @method static Builder<static>|ItemBillComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ItemBillComponent withUsers()
 * @method static Builder<static>|ItemBillComponent withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(ItemBillComponentResource::class)]
class ItemBillComponent extends ModelWithCompanyAbstract
{
    /** @use HasFactory<\Database\Factories\Items\ItemBillComponentFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'item_bill_id',
        'item_id',
        'quantity',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * @return BelongsTo<ItemBill, $this>
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(ItemBill::class, 'item_bill_id');
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
        ];
    }
}
