<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\Purchases\PurchaseReturnResource;
use App\Models\Purchases\PurchaseReturn;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseReturnController extends Controller
{
    /**
     * Purchase Return Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, PurchaseReturn>|Collection<int, PurchaseReturn>|JsonResource|int
     *
     * @noinspection DuplicatedCode
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $purchaseReturns = PurchaseReturn::query()
            ->withContributors()
            ->withUsers()
            ->with([
                'components',
                'goodReceipt',
                'order',
            ])->when($request->input('search'), function (Builder $query) use ($request) {
                return $query->where(function (Builder $query) use ($request) {
                    return $query->where('code', 'like', '%'.$request->input('search').'%')
                        ->orWhere('note', 'like', '%'.$request->input('search').'%');
                });
            })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return PurchaseReturnResource::collection($purchaseReturns->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $purchaseReturns->count();
        }

        return PurchaseReturnResource::collection($purchaseReturns->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Purchase Return Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'good_receipt_id' => ['required', 'string', 'exists:good_receipts,id'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Purchase Return', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseReturn = new PurchaseReturn;
        $purchaseReturn->code = CodeGeneratorService::code('PR')->number(PurchaseReturn::count())->generate();
        $purchaseReturn->purchase_order_id = $request->input('purchase_order_id');
        $purchaseReturn->good_receipt_id = $request->input('good_receipt_id');
        $purchaseReturn->total = 0;
        $purchaseReturn->note = $request->input('note');
        $purchaseReturn->save();

        $purchaseReturn->initEvent($user);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return PurchaseReturn::query()
            ->withContributors()
            ->withUsers()
            ->with([
                'components',
                'goodReceipt',
                'order',
            ])->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Purchase Return Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'good_receipt_id' => ['required', 'string', 'exists:good_receipts,id'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();
        $purchaseReturn->purchase_order_id = $request->input('purchase_order_id');
        $purchaseReturn->good_receipt_id = $request->input('good_receipt_id');
        $purchaseReturn->total = $purchaseReturn->components->sum('total');
        $purchaseReturn->note = $request->input('note');
        $purchaseReturn->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();
        $purchaseReturn->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseReturn->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseReturn->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Approves
     *
     * Approve the specified purchase return.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Return', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseReturn->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Reject
     *
     * Reject the specified purchase return.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Return', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseReturn->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Cancel
     *
     * Cancel the specified purchase return.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Return', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseReturn->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Rollback
     *
     * Roll back the specified purchase return.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Return', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseReturn->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }

    /**
     * Purchase Return Force
     *
     * Force execute action on the specified purchase return.
     *
     * @return array{message: string, purchase_return: JsonResource}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = PurchaseReturn::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Return', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseReturn->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Return'], App::getLocale()),
            'purchase_return' => $purchaseReturn->toResource(),
        ];
    }
}
