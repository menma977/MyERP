<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseRequest;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseRequestController extends Controller
{
    /**
     * Purchase Request Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseRequest>|Collection<int, PurchaseRequest>
     *
     * @noinspection DuplicatedCode
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseRequests = PurchaseRequest::with([
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('code', 'like', '%' . $request->input('search') . '%');
            });
        })->when(function () use ($request) {
            return $request->boolean('is_approved') || $request->boolean('is_canceled') || $request->boolean('is_rejected') || $request->boolean('is_rollback');
        }, function (Builder $query) use ($request) {
            $query->where(function (Builder $query) use ($request) {
                if ($request->boolean('is_approved')) {
                    $query->orWhereHas('event', fn(Builder $query) => $query->whereNotNull('approved_at'));
                }
                if ($request->boolean('is_canceled')) {
                    $query->orWhereHas('event', fn(Builder $query) => $query->whereNotNull('cancelled_at'));
                }
                if ($request->boolean('is_rejected')) {
                    $query->orWhereHas('event', fn(Builder $query) => $query->whereNotNull('rejected_at'));
                }
                if ($request->boolean('is_rollback')) {
                    $query->orWhereHas('event', fn(Builder $query) => $query->whereNotNull('rollback_at'));
                }
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseRequests->get();
        }

        return $purchaseRequests->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Request Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Purchase Request', 'target' => 'Access']),
            ]);
        }

        $purchaseRequest = new PurchaseRequest;
        $purchaseRequest->code = CodeGeneratorService::code('PR')->number(PurchaseRequest::count())->generate();
        $purchaseRequest->total = $request->input('total');
        $purchaseRequest->save();

        $purchaseRequest->initEvent($user);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): PurchaseRequest
    {
        return PurchaseRequest::with([
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Request Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));
        $purchaseRequest->total = $request->input('total');
        $purchaseRequest->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));

        if ($purchaseRequest->procurement) {
            throw ValidationException::withMessages([
                'procurement' => trans('messages.fail.action.cost', ['action' => 'delete', 'attribute' => 'Purchase Request', 'target' => 'Procurement']),
            ]);
        }

        if ($purchaseRequest->order) {
            throw ValidationException::withMessages([
                'order' => trans('messages.fail.action.cost', ['action' => 'delete', 'attribute' => 'Purchase Request', 'target' => 'Order']),
            ]);
        }

        $purchaseRequest->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseRequest->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseRequest->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Approves
     *
     * Approve the specified purchase request.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Request', 'target' => 'Access']),
            ]);
        }

        $purchaseRequest->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Reject
     *
     * Reject the specified purchase request.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Request', 'target' => 'Access']),
            ]);
        }

        $purchaseRequest->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Cancel
     *
     * Cancel the specified purchase request.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Request', 'target' => 'Access']),
            ]);
        }

        $purchaseRequest->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Rollback
     *
     * Roll back the specified purchase request.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Request', 'target' => 'Access']),
            ]);
        }

        $purchaseRequest->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Request']),
        ];
    }

    /**
     * Purchase Request Force
     *
     * Force execute action on the specified purchase request.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Request', 'target' => 'Access']),
            ]);
        }

        $purchaseRequest->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Request']),
        ];
    }
}
