<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\Purchases\PurchaseOrderComponentResource;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class PurchaseOrderComponentController extends Controller
{
    /**
     * Purchase Order Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseOrderComponent>|Collection<int, PurchaseOrderComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $purchaseOrderComponents = PurchaseOrderComponent::with([
            'order',
            'requestComponent',
            'procurementComponent',
            'item',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query->where('note', 'like', '%'.$request->input('search').'%')
                    ->orWhereHas('item', function (Builder $query) use ($request) {
                        $query->where('name', 'like', '%'.$request->input('search').'%');
                    });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return PurchaseOrderComponentResource::collection($purchaseOrderComponents->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $purchaseOrderComponents->count();
        }

        return PurchaseOrderComponentResource::collection($purchaseOrderComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Purchase Order Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, purchase_order_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $purchaseOrderComponent = new PurchaseOrderComponent;
        $this->save($request, $purchaseOrderComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Order Component'], App::getLocale()),
            'purchase_order_component' => $purchaseOrderComponent->toResource(),
        ];
    }

    /**
     * Purchase Order Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return PurchaseOrderComponent::with([
            'order',
            'requestComponent',
            'procurementComponent',
            'item',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Purchase Order Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, purchase_order_component: JsonResource}
     */
    public function update(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::where('id', $request->route('id'))->firstOrFail();
        $this->save($request, $purchaseOrderComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Order Component'], App::getLocale()),
            'purchase_order_component' => $purchaseOrderComponent->toResource(),
        ];
    }

    /**
     * Purchase Order Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, purchase_order_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::where('id', $request->route('id'))->firstOrFail();
        $purchaseOrderComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Order Component'], App::getLocale()),
            'purchase_order_component' => $purchaseOrderComponent->toResource(),
        ];
    }

    /**
     * Purchase Order Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, purchase_order_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseOrderComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Order Component'], App::getLocale()),
            'purchase_order_component' => $purchaseOrderComponent->toResource(),
        ];
    }

    /**
     * Purchase Order Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, purchase_order_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseOrderComponent $purchaseOrderComponent */
        $purchaseOrderComponent = PurchaseOrderComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseOrderComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Order Component'], App::getLocale()),
            'purchase_order_component' => $purchaseOrderComponent->toResource(),
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
