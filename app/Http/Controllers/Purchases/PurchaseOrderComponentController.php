<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseOrderComponentController extends Controller
{
    /**
     * Purchase Order Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseOrderComponent>|Collection<int, PurchaseOrderComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseOrderComponents = PurchaseOrderComponent::with([
            'order',
            'requestComponent',
            'procurementComponent',
            'item',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query->where('note', 'like', '%'.$request->input('search').'%')
                    ->orWhereHas('item', function (Builder $query) use ($request) {
                        $query->where('name', 'like', '%'.$request->input('search').'%');
                    });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseOrderComponents->get();
        }

        return $purchaseOrderComponents->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Order Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $purchaseOrderComponent = new PurchaseOrderComponent;
        $this->save($request, $purchaseOrderComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Order Component']),
        ];
    }

    /**
     * Purchase Order Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): PurchaseOrderComponent
    {
        return PurchaseOrderComponent::with([
            'order',
            'requestComponent',
            'procurementComponent',
            'item',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Order Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::findOrFail($request->route('id'));
        $this->save($request, $purchaseOrderComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Order Component']),
        ];
    }

    /**
     * Purchase Order Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::findOrFail($request->route('id'));
        $purchaseOrderComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Order Component']),
        ];
    }

    /**
     * Purchase Order Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseOrderComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Order Component']),
        ];
    }

    /**
     * Purchase Order Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseOrderComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Order Component']),
        ];
    }

    /**
     * Save the purchase order component and update the total amount in the purchase order.
     */
    protected function save(Request $request, PurchaseOrderComponent $purchaseOrderComponent): void
    {
        $request->validate([
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'purchase_request_component_id' => ['nullable', 'string', 'exists:purchase_request_components,id'],
            'purchase_procurement_component_id' => ['nullable', 'string', 'exists:purchase_procurement_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'request_quantity' => ['required', 'numeric', 'min:0'],
            'request_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $purchaseOrderComponent->purchase_order_id = $request->input('purchase_order_id');
        $purchaseOrderComponent->purchase_request_component_id = $request->input('purchase_request_component_id');
        $purchaseOrderComponent->purchase_procurement_component_id = $request->input('purchase_procurement_component_id');
        $purchaseOrderComponent->item_id = $request->input('item_id');
        $purchaseOrderComponent->request_quantity = $request->float('request_quantity');
        $purchaseOrderComponent->request_price = $request->float('request_price');
        $purchaseOrderComponent->request_total = $purchaseOrderComponent->request_quantity * $purchaseOrderComponent->request_price;
        $purchaseOrderComponent->quantity = $request->float('quantity');
        $purchaseOrderComponent->price = $request->float('price');
        $purchaseOrderComponent->total = $purchaseOrderComponent->quantity * $purchaseOrderComponent->price;
        $purchaseOrderComponent->note = $request->input('note');
        $purchaseOrderComponent->save();

        $purchaseOrder = PurchaseOrder::find($purchaseOrderComponent->purchase_order_id);
        if (! $purchaseOrder) {
            return;
        }

        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder->total = (float) $purchaseOrder->components()->sum('total');
        $purchaseOrder->save();
    }
}
