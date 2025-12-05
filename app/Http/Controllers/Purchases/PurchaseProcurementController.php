<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseProcurement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseProcurementController extends Controller
{
    /**
     * Purchase Procurement Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseProcurement>|Collection<int, PurchaseProcurement>
     *
     * @noinspection DuplicatedCode
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseProcurements = PurchaseProcurement::with([
            'request',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query->where('code', 'like', '%'.$request->input('search').'%');
            });
        })->when(function () use ($request) {
            return $request->boolean('is_approved') || $request->boolean('is_canceled') || $request->boolean('is_rejected') || $request->boolean('is_rollback');
        }, function (Builder $query) use ($request) {
            $query->where(function (Builder $query) use ($request) {
                if ($request->boolean('is_approved')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('approved_at'));
                }
                if ($request->boolean('is_canceled')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('cancelled_at'));
                }
                if ($request->boolean('is_rejected')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('rejected_at'));
                }
                if ($request->boolean('is_rollback')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('rollback_at'));
                }
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseProcurements->get();
        }

        return $purchaseProcurements->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Procurement Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): PurchaseProcurement
    {
        return PurchaseProcurement::with([
            'request',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Procurement Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_request_id' => ['required', 'string', 'exists:purchase_requests,id'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::findOrFail($request->route('id'));
        $purchaseProcurement->note = $request->input('note');
        $purchaseProcurement->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseProcurement->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseProcurement->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Approves
     *
     * Approve the specified purchase procurement.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Procurement', 'target' => 'Access']),
            ]);
        }

        $purchaseProcurement->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Reject
     *
     * Reject the specified purchase procurement.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Procurement', 'target' => 'Access']),
            ]);
        }

        $purchaseProcurement->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Cancel
     *
     * Cancel the specified purchase procurement.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Procurement', 'target' => 'Access']),
            ]);
        }

        $purchaseProcurement->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Rollback
     *
     * Roll back the specified purchase procurement.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Procurement', 'target' => 'Access']),
            ]);
        }

        $purchaseProcurement->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Procurement']),
        ];
    }

    /**
     * Purchase Procurement Force
     *
     * Force execute action on the specified purchase procurement.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Procurement', 'target' => 'Access']),
            ]);
        }

        $purchaseProcurement->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Procurement']),
        ];
    }
}
