<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\GoodReceipt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GoodReceiptController extends Controller
{
    /**
     * Good Receipt Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, GoodReceipt>|Collection<int, GoodReceipt>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $goodReceipts = GoodReceipt::with([
            'order',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('code', 'like', '%'.$request->input('search').'%')->orWhereHas('order', function (Builder $query) use ($request) {
                    $query->where('code', 'like', '%'.$request->input('search').'%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $goodReceipts->get();
        }

        return $goodReceipts->paginate($request->input('per_page', 10));
    }

    /**
     * Good Receipt Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): GoodReceipt
    {
        return GoodReceipt::with([
            'order',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Good Receipt Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));
        $goodReceipt->purchase_order_id = $request->input('purchase_order_id');
        $goodReceipt->note = $request->input('note');
        $goodReceipt->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));

        if ($goodReceipt->purchaseReturns()->exists()) {
            throw ValidationException::withMessages([
                'purchase_returns' => trans('messages.fail.action.cost', ['action' => 'delete', 'attribute' => 'Good Receipt', 'target' => 'Purchase Returns']),
            ]);
        }

        $goodReceipt->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::onlyTrashed()->findOrFail($request->route('id'));
        $goodReceipt->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::onlyTrashed()->findOrFail($request->route('id'));
        $goodReceipt->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Approves
     *
     * Approve the specified resource.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Good Receipt', 'target' => 'Access']),
            ]);
        }
        $goodReceipt->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Reject
     *
     * Reject the specified resource.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Good Receipt', 'target' => 'Access']),
            ]);
        }
        $goodReceipt->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Cancel
     *
     * Cancel the specified resource.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Good Receipt', 'target' => 'Access']),
            ]);
        }
        $goodReceipt->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Rollback
     *
     * Roll back the specified resource.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Good Receipt', 'target' => 'Access']),
            ]);
        }
        $goodReceipt->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Good Receipt']),
        ];
    }

    /**
     * Good Receipt Force
     *
     * Force execute action on the specified resource.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        /** @var GoodReceipt $goodReceipt */
        $goodReceipt = GoodReceipt::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Good Receipt', 'target' => 'Access']),
            ]);
        }
        $goodReceipt->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Good Receipt']),
        ];
    }
}
