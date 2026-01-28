<?php

namespace App\Models\Transactions;

use App\Abstracts\ApprovalAbstract;
use App\Enums\ExpenseCategoryEnum;
use App\Enums\PaymentMethodEnum;
use App\Http\Resources\Transactions\ExpenseResource;
use App\Models\Approval\ApprovalEvent;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Represents an Expense transaction in the system.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $code
 * @property ExpenseCategoryEnum $category
 * @property PaymentMethodEnum $method
 * @property numeric $total
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
 * @method static Builder<static>|Expense newModelQuery()
 * @method static Builder<static>|Expense newQuery()
 * @method static Builder<static>|Expense onlyTrashed()
 * @method static Builder<static>|Expense query()
 * @method static Builder<static>|Expense withContributors()
 * @method static Builder<static>|Expense withUsers()
 * @method static Builder<static>|Expense withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Expense withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[UseResource(ExpenseResource::class)]
class Expense extends ApprovalAbstract
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
        'total',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'category' => ExpenseCategoryEnum::class,
            'method' => PaymentMethodEnum::class,
        ];
    }

    protected function onApprove(ApprovalEvent $approvalEvent): void
    {
        if ($approvalEvent->is_approved) {
            /** @noinspection PhpUnhandledExceptionInspection */
            DB::transaction(function () use ($approvalEvent) {
                $expense = Expense::lockForUpdate()->find($approvalEvent->requestable_id);
                if (! $expense) {
                    $approvalEvent->approved_at = null;
                    $approvalEvent->save();

                    throw ValidationException::withMessages([
                        'id' => trans('messages.fail.approve', ['target' => 'Expense']),
                    ]);
                }

                $latestLedgerTotal = Ledger::latest()->value('total') ?? 0;

                $ledger = new Ledger;
                $ledger->code = CodeGeneratorService::code('LDG-EXP')->number(Ledger::count())->generate();
                $ledger->in = 0;
                $ledger->out = $expense->total;
                $ledger->total = $latestLedgerTotal - $expense->total;
                $ledger->save();

                $ledgerComponent = new LedgerComponent;
                $ledgerComponent->ledger_id = $ledger->id;
                $ledgerComponent->in = 0;
                $ledgerComponent->out = $expense->total;
                $ledgerComponent->total = $ledger->total;
                $ledgerComponent->save();
            });
        }
    }
}
