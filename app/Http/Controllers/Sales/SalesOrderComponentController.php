<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\Sales\SalesOrderComponentResource;
use App\Models\Sales\SalesOrderComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesOrderComponentController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, SalesOrderComponent>|Collection<int, SalesOrderComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $salesOrderComponents = SalesOrderComponent::with(['order', 'item'])
            ->when($request->input('search'), function ($query) use ($request) {
                $query->whereHas('item', function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('search').'%');
                });
            })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return SalesOrderComponentResource::collection($salesOrderComponents->get());
        }

        if ($request->input('type') === 'count') {
            return $salesOrderComponents->count();
        }

        return SalesOrderComponentResource::collection($salesOrderComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * @return array{message: string, sales_order_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'sales_order_id' => ['required', 'ulid', 'exists:sales_orders,id'],
            'item_id' => ['required', 'ulid', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $salesOrderComponent = new SalesOrderComponent;
        $salesOrderComponent->sales_order_id = $request->input('sales_order_id');
        $salesOrderComponent->item_id = $request->input('item_id');
        $salesOrderComponent->quantity = $request->input('quantity');
        $salesOrderComponent->price = $request->input('price');
        $salesOrderComponent->total = $request->input('total');
        $salesOrderComponent->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Sales Order Component']),
            'sales_order_component' => $salesOrderComponent->toResource(),
        ];
    }

    public function show(Request $request): JsonResource
    {
        return SalesOrderComponent::with(['order', 'item'])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * @return array{message: string, sales_order_component: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'sales_order_id' => ['required', 'ulid', 'exists:sales_orders,id'],
            'item_id' => ['required', 'ulid', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var SalesOrderComponent $salesOrderComponent */
        $salesOrderComponent = SalesOrderComponent::where('id', $request->route('id'))->firstOrFail();
        $salesOrderComponent->sales_order_id = $request->input('sales_order_id');
        $salesOrderComponent->item_id = $request->input('item_id');
        $salesOrderComponent->quantity = $request->input('quantity');
        $salesOrderComponent->price = $request->input('price');
        $salesOrderComponent->total = $request->input('total');
        $salesOrderComponent->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Sales Order Component']),
            'sales_order_component' => $salesOrderComponent->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var SalesOrderComponent $salesOrderComponent */
        $salesOrderComponent = SalesOrderComponent::where('id', $request->route('id'))->firstOrFail();
        $salesOrderComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Sales Order Component']),
            'sales_order_component' => $salesOrderComponent->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var SalesOrderComponent $salesOrderComponent */
        $salesOrderComponent = SalesOrderComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesOrderComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Sales Order Component']),
            'sales_order_component' => $salesOrderComponent->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesOrderComponent $salesOrderComponent */
        $salesOrderComponent = SalesOrderComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesOrderComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Sales Order Component']),
            'sales_order_component' => $salesOrderComponent->toResource(),
        ];
    }
}
