<?php

namespace App\Http\Controllers\Transactions;

use App\Enums\ExpenseCategoryEnum;
use App\Enums\PaymentMethodEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transactions\ExpenseResource;
use App\Models\Transactions\Expense;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    /**
     * Expense Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, Expense>|Collection<int, Expense>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $Expenses = Expense::withContributors()->withUsers()->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%')
                ->orWhere('note', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return ExpenseResource::collection($Expenses->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $Expenses->count();
        }

        return ExpenseResource::collection($Expenses->paginate($request->input('per_page', 10)));
    }

    /**
     * Expense Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, Expense: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'category' => ['required', Rule::enum(ExpenseCategoryEnum::class)],
            'method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Expense', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expense = new Expense;
        $expense->code = CodeGeneratorService::code('EXP')->number(Expense::count())->generate();
        $expense->category = $request->input('category');
        $expense->method = $request->input('method');
        $expense->amount = $request->input('amount');
        $expense->note = $request->input('note');
        $expense->save();

        $expense->initEvent($user);

        return [
            'message' => trans('messages.success.store', ['target' => 'Expense'], App::getLocale()),
            'Expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        /** @var Expense $expense */
        $expense = Expense::withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail();

        return $expense->toResource();
    }

    /**
     * Expense Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'category' => ['required', Rule::enum(ExpenseCategoryEnum::class)],
            'method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();
        $expense->category = $request->input('category');
        $expense->method = $request->input('method');
        $expense->amount = $request->input('amount');
        $expense->note = $request->input('note');
        $expense->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();
        $expense->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $expense->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $expense->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Approves
     *
     * Approve the specified resource.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'expense', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expense->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Reject
     *
     * Reject the specified resource.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'expense', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expense->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * Expense Cancel
     *
     * Cancel the specified resource.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'expense', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expense->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * expense Rollback
     *
     * Roll back the specified resource.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'expense', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expense->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }

    /**
     * expense Force
     *
     * Force execute action on the specified resource.
     *
     * @return array{message: string, expense: JsonResource}
     */
    public function force(Request $request): array
    {
        /** @var Expense $expense */
        $expense = Expense::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'expense', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expense->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'expense'], App::getLocale()),
            'expense' => $expense->toResource(),
        ];
    }
}
