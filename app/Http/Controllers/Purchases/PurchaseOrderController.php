<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Purchase Order Controller
 *
 * Handles CRUD operations for Purchase Orders.
 */
class PurchaseOrderController extends Controller
{
    /**
     * Purchase Order Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseOrder>|Collection<int, PurchaseOrder>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseOrders = PurchaseOrder::with([
            'request',
            'procurement',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('code', 'like', '%' . $request->input('search') . '%')
                    ->orWhereHas('request', function (Builder $query) use ($request) {
                        $query->where('code', 'like', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('procurement', function (Builder $query) use ($request) {
                        $query->where('code', 'like', '%' . $request->input('search') . '%');
                    });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseOrders->get();
        }

        return $purchaseOrders->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Order Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): PurchaseOrder
    {
        return PurchaseOrder::with([
            'request',
            'procurement',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Order Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_request_id' => ['required', 'string', 'exists:purchase_requests,id'],
            'purchase_procurement_id' => ['required', 'string', 'exists:purchase_procurements,id'],
            'request_total' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));
        $purchaseOrder->purchase_request_id = $request->input('purchase_request_id');
        $purchaseOrder->purchase_procurement_id = $request->input('purchase_procurement_id');
        $purchaseOrder->request_total = $request->input('request_total');
        $purchaseOrder->total = $request->input('total');
        $purchaseOrder->note = $request->input('note');
        $purchaseOrder->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));

        if ($purchaseOrder->return) {
            throw ValidationException::withMessages([
                'return' => trans('messages.fail.action.cost', ['action' => 'delete', 'attribute' => 'Purchase Order', 'target' => 'Return']),
            ]);
        }

        $purchaseOrder->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseOrder->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseOrder->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Approves
     *
     * Approve the specified resource.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Order', 'target' => 'Access']),
            ]);
        }

        $purchaseOrder->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Reject
     *
     * Reject the specified resource.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Order', 'target' => 'Access']),
            ]);
        }

        $purchaseOrder->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Cancel
     *
     * Cancel the specified resource.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Order', 'target' => 'Access']),
            ]);
        }

        $purchaseOrder->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Rollback
     *
     * Roll back the specified resource.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Order', 'target' => 'Access']),
            ]);
        }

        $purchaseOrder->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Order']),
        ];
    }

    /**
     * Purchase Order Force
     *
     * Force execute action on the specified resource.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Order', 'target' => 'Access']),
            ]);
        }

        $purchaseOrder->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Order']),
        ];
    }
}
