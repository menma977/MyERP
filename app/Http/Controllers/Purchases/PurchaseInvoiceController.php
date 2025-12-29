<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseInvoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Purchase Invoice Controller
 *
 * Handles CRUD operations for Purchase Invoices.
 */
class PurchaseInvoiceController extends Controller
{
    /**
     * Purchase Invoice Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, PurchaseInvoice>|Collection<int, PurchaseInvoice>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseInvoices = PurchaseInvoice::with([
            'order',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('code', 'like', '%' . $request->input('search') . '%')->orWhereHas('order', function (Builder $query) use ($request) {
                    $query->where('code', 'like', '%' . $request->input('search') . '%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseInvoices->get();
        }

        return $purchaseInvoices->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Invoice Show
     *
     * Show specified resource.
     */
    public function show(Request $request): PurchaseInvoice
    {
        return PurchaseInvoice::with([
            'order',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Invoice Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'tax' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));
        $purchaseInvoice->purchase_order_id = $request->input('purchase_order_id');
        $purchaseInvoice->total = $purchaseInvoice->components->sum('total');
        $purchaseInvoice->tax = PurchaseInvoice::TAX * $purchaseInvoice->total;
        $purchaseInvoice->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));
        $purchaseInvoice->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseInvoice->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseInvoice->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Approves
     *
     * Approve specified resource.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Invoice', 'target' => 'Access']),
            ]);
        }

        $purchaseInvoice->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Reject
     *
     * Reject specified resource.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Invoice', 'target' => 'Access']),
            ]);
        }

        $purchaseInvoice->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Cancel
     *
     * Cancel specified resource.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Invoice', 'target' => 'Access']),
            ]);
        }

        $purchaseInvoice->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Rollback
     *
     * Roll back specified resource.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Invoice', 'target' => 'Access']),
            ]);
        }

        $purchaseInvoice->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Invoice']),
        ];
    }

    /**
     * Purchase Invoice Force
     *
     * Force to execute action on a specified resource.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice = PurchaseInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Invoice', 'target' => 'Access']),
            ]);
        }

        $purchaseInvoice->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Invoice']),
        ];
    }
}
