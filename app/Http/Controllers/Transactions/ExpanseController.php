<?php

namespace App\Http\Controllers\Transactions;

use App\Enums\ExpanseCategoryEnum;
use App\Enums\PaymentMethodEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transactions\ExpanseResource;
use App\Models\Transactions\Expanse;
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

class ExpanseController extends Controller
{
    /**
     * Expanse Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, Expanse>|Collection<int, Expanse>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $expanses = Expanse::withContributors()->withUsers()->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%')
                ->orWhere('note', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return ExpanseResource::collection($expanses->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $expanses->count();
        }

        return ExpanseResource::collection($expanses->paginate($request->input('per_page', 10)));
    }

    /**
     * Expanse Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'category' => ['required', Rule::enum(ExpanseCategoryEnum::class)],
            'method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Expanse', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expanse = new Expanse;
        $expanse->code = CodeGeneratorService::code('EXP')->number(Expanse::count())->generate();
        $expanse->category = $request->input('category');
        $expanse->method = $request->input('method');
        $expanse->amount = $request->input('amount');
        $expanse->note = $request->input('note');
        $expanse->save();

        $expanse->initEvent($user);

        return [
            'message' => trans('messages.success.store', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail();

        return $expanse->toResource();
    }

    /**
     * Expanse Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'category' => ['required', Rule::enum(ExpanseCategoryEnum::class)],
            'method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();
        $expanse->category = $request->input('category');
        $expanse->method = $request->input('method');
        $expanse->amount = $request->input('amount');
        $expanse->note = $request->input('note');
        $expanse->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();
        $expanse->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $expanse->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $expanse->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Approves
     *
     * Approve the specified resource.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Expanse', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expanse->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Reject
     *
     * Reject the specified resource.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Expanse', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expanse->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Cancel
     *
     * Cancel the specified resource.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Expanse', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expanse->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Rollback
     *
     * Roll back the specified resource.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Expanse', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expanse->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }

    /**
     * Expanse Force
     *
     * Force execute action on the specified resource.
     *
     * @return array{message: string, expanse: JsonResource}
     */
    public function force(Request $request): array
    {
        /** @var Expanse $expanse */
        $expanse = Expanse::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Expanse', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $expanse->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Expanse'], App::getLocale()),
            'expanse' => $expanse->toResource(),
        ];
    }
}
