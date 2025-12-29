<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Transactions\PaymentRequest;
use App\Models\Transactions\PaymentRequestComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Payment Request Component Controller
 *
 * Handles CRUD operations for Payment Request Components.
 */
class PaymentRequestComponentController extends Controller
{
    /**
     * Payment Request Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, PaymentRequestComponent>|Collection<int, PaymentRequestComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $paymentRequestComponents = PaymentRequestComponent::with([
            'paymentRequest',
            'purchaseOrderComponent',
            'purchaseInvoiceComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('paymentRequest', function (Builder $query) use ($request) {
                    $query->where('code', 'like', '%' . $request->input('search') . '%');
                })->orWhereHas('purchaseOrderComponent', function (Builder $query) use ($request) {
                    $query->where('item_id', 'like', '%' . $request->input('search') . '%');
                })->orWhereHas('purchaseInvoiceComponent', function (Builder $query) use ($request) {
                    $query->where('item_id', 'like', '%' . $request->input('search') . '%');
                });
            });
        })->where('payment_request_id', $request->route('payment_request_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $paymentRequestComponents->get();
        }

        return $paymentRequestComponents->paginate($request->input('per_page', 10));
    }

    /**
     * Payment Request Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): PaymentRequestComponent
    {
        return PaymentRequestComponent::with([
            'paymentRequest',
            'purchaseOrderComponent',
            'purchaseInvoiceComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Payment Request Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'payment_request_id' => ['required', 'string', 'exists:payment_requests,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'purchase_invoice_component_id' => ['required', 'string', 'exists:purchase_invoice_components,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $paymentRequestComponent = new PaymentRequestComponent;
        $this->save($request, $paymentRequestComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Payment Request Component']),
        ];
    }

    /**
     * Payment Request Component Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'payment_request_id' => ['required', 'string', 'exists:payment_requests,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'purchase_invoice_component_id' => ['required', 'string', 'exists:purchase_invoice_components,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var PaymentRequestComponent $paymentRequestComponent */
        $paymentRequestComponent = PaymentRequestComponent::findOrFail($request->route('id'));
        $this->save($request, $paymentRequestComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Payment Request Component']),
        ];
    }

    /**
     * Payment Request Component Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PaymentRequestComponent $paymentRequestComponent */
        $paymentRequestComponent = PaymentRequestComponent::findOrFail($request->route('id'));
        $paymentRequestComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Payment Request Component']),
        ];
    }

    /**
     * Payment Request Component Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PaymentRequestComponent $paymentRequestComponent */
        $paymentRequestComponent = PaymentRequestComponent::onlyTrashed()->findOrFail($request->route('id'));
        $paymentRequestComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Payment Request Component']),
        ];
    }

    /**
     * Payment Request Component Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PaymentRequestComponent $paymentRequestComponent */
        $paymentRequestComponent = PaymentRequestComponent::onlyTrashed()->findOrFail($request->route('id'));
        $paymentRequestComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Payment Request Component']),
        ];
    }

    protected function save(Request $request, PaymentRequestComponent $paymentRequestComponent): void
    {
        $paymentRequestComponent->payment_request_id = $request->input('payment_request_id');
        $paymentRequestComponent->purchase_order_component_id = $request->input('purchase_order_component_id');
        $paymentRequestComponent->purchase_invoice_component_id = $request->input('purchase_invoice_component_id');
        $paymentRequestComponent->quantity = $request->input('quantity');
        $paymentRequestComponent->price = $request->input('price');
        $paymentRequestComponent->note = $request->input('note');
        $paymentRequestComponent->total = $paymentRequestComponent->price * $paymentRequestComponent->quantity;
        $paymentRequestComponent->save();

        $paymentRequest = PaymentRequest::find($paymentRequestComponent->payment_request_id);
        if (!$paymentRequest) {
            return;
        }

        /** @var PaymentRequest $paymentRequest */
        $paymentRequest->total = (float)$paymentRequest->components()->sum('total');
        $paymentRequest->save();
    }
}
