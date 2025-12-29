<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Transactions\PaymentRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaymentRequestController extends Controller
{
    /**
     * Payment Request Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, PaymentRequest>|Collection<int, PaymentRequest>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $paymentRequests = PaymentRequest::with([
            'order',
            'invoice',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('code', 'like', '%' . $request->input('search') . '%')
                    ->orWhereHas('order', function (Builder $query) use ($request) {
                        $query->where('code', 'like', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('invoice', function (Builder $query) use ($request) {
                        $query->where('code', 'like', '%' . $request->input('search') . '%');
                    });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $paymentRequests->get();
        }

        return $paymentRequests->paginate($request->input('per_page', 10));
    }

    /**
     * Payment Request Show
     *
     * Show specified resource.
     */
    public function show(Request $request): PaymentRequest
    {
        return PaymentRequest::with([
            'order',
            'invoice',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Payment Request Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'purchase_invoice_id' => ['required', 'string', 'exists:purchase_invoices,id'],
            'method' => ['required', 'string', 'in:cash,transfer,check,other'],
            'total' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));
        $paymentRequest->purchase_order_id = $request->input('purchase_order_id');
        $paymentRequest->purchase_invoice_id = $request->input('purchase_invoice_id');
        $paymentRequest->code = $request->input('code');
        $paymentRequest->method = $request->input('method');
        $paymentRequest->total = $request->input('total');
        $paymentRequest->tax = $request->input('tax');
        $paymentRequest->note = $request->input('note');
        $paymentRequest->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));
        $paymentRequest->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::onlyTrashed()->findOrFail($request->route('id'));
        $paymentRequest->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::onlyTrashed()->findOrFail($request->route('id'));
        $paymentRequest->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Approve
     *
     * Approve specified resource.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Payment Request', 'target' => 'Access']),
            ]);
        }

        $paymentRequest->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Reject
     *
     * Reject specified resource.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Payment Request', 'target' => 'Access']),
            ]);
        }

        $paymentRequest->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Cancel
     *
     * Cancel specified resource.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Payment Request', 'target' => 'Access']),
            ]);
        }

        $paymentRequest->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Rollback
     *
     * Roll back specified resource.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Payment Request', 'target' => 'Access']),
            ]);
        }

        $paymentRequest->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Payment Request']),
        ];
    }

    /**
     * Payment Request Force
     *
     * Force to execute action on a specified resource.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        $request->validate([
            'step' => ['required', 'string'],
        ]);

        /** @var PaymentRequest $paymentRequest */
        $paymentRequest = PaymentRequest::findOrFail($request->route('id'));

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Payment Request', 'target' => 'Access']),
            ]);
        }

        $paymentRequest->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Payment Request']),
        ];
    }
}
