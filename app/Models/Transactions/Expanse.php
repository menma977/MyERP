<?php

namespace App\Models\Transactions;

use App\Abstracts\ApprovalAbstract;
use App\Enums\ExpanseCategoryEnum;
use App\Enums\PaymentMethodEnum;
use App\Http\Resources\Transactions\ExpanseResource;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents an Expanse transaction in the system.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $code
 * @property ExpanseCategoryEnum $category
 * @property PaymentMethodEnum $method
 * @property string $amount
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Models\Companies\Company|null $company
 * @property-read \App\Models\Approval\ApprovalEvent|null $event
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $updatedBy
 * @property-read \App\Models\User|null $deletedBy
 *
 * @method static Builder<static>|Expanse newModelQuery()
 * @method static Builder<static>|Expanse newQuery()
 * @method static Builder<static>|Expanse onlyTrashed()
 * @method static Builder<static>|Expanse query()
 * @method static Builder<static>|Expanse withContributors()
 * @method static Builder<static>|Expanse withUsers()
 * @method static Builder<static>|Expanse withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Expanse withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[UseResource(ExpanseResource::class)]
class Expanse extends ApprovalAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'category',
        'method',
        'amount',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'category' => ExpanseCategoryEnum::class,
            'method' => PaymentMethodEnum::class,
        ];
    }
}
