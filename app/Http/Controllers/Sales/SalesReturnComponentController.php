<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\Sales\SalesReturnComponentResource;
use App\Models\Sales\SalesReturnComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesReturnComponentController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, SalesReturnComponent>|Collection<int, SalesReturnComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $salesReturnComponents = SalesReturnComponent::with([
            'return',
            'item',
        ])->when($request->input('search'), function ($query) use ($request) {
            $query->whereHas('item', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return SalesReturnComponentResource::collection($salesReturnComponents->get());
        }

        if ($request->input('type') === 'count') {
            return $salesReturnComponents->count();
        }

        return SalesReturnComponentResource::collection($salesReturnComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * @return array{message: string, sales_return_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'sales_return_id' => ['required', 'ulid', 'exists:sales_returns,id'],
            'item_id' => ['required', 'ulid', 'exists:items,id'],
            'item_batch_id' => ['nullable', 'ulid', 'exists:item_batches,id'],
            'item_stock_id' => ['nullable', 'ulid', 'exists:item_stocks,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $salesReturnComponent = new SalesReturnComponent;
        $this->save($request, $salesReturnComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Sales Return Component']),
            'sales_return_component' => $salesReturnComponent->toResource(),
        ];
    }

    public function show(Request $request): JsonResource
    {
        return SalesReturnComponent::with([
            'return',
            'item',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * @return array{message: string, sales_return_component: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'sales_return_id' => ['required', 'ulid', 'exists:sales_returns,id'],
            'item_id' => ['required', 'ulid', 'exists:items,id'],
            'item_batch_id' => ['nullable', 'ulid', 'exists:item_batches,id'],
            'item_stock_id' => ['nullable', 'ulid', 'exists:item_stocks,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var SalesReturnComponent $salesReturnComponent */
        $salesReturnComponent = SalesReturnComponent::where('id', $request->route('id'))->firstOrFail();
        $this->save($request, $salesReturnComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Sales Return Component']),
            'sales_return_component' => $salesReturnComponent->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_return_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var SalesReturnComponent $salesReturnComponent */
        $salesReturnComponent = SalesReturnComponent::where('id', $request->route('id'))->firstOrFail();
        $salesReturnComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Sales Return Component']),
            'sales_return_component' => $salesReturnComponent->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_return_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var SalesReturnComponent $salesReturnComponent */
        $salesReturnComponent = SalesReturnComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesReturnComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Sales Return Component']),
            'sales_return_component' => $salesReturnComponent->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_return_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesReturnComponent $salesReturnComponent */
        $salesReturnComponent = SalesReturnComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesReturnComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Sales Return Component']),
            'sales_return_component' => $salesReturnComponent->toResource(),
        ];
    }

    protected function save(Request $request, SalesReturnComponent $salesReturnComponent): void
    {
        $salesReturnComponent->sales_return_id = $request->input('sales_return_id');
        /** @noinspection DuplicatedCode */
        $salesReturnComponent->item_id = $request->input('item_id');
        $salesReturnComponent->item_batch_id = $request->input('item_batch_id');
        $salesReturnComponent->item_stock_id = $request->input('item_stock_id');
        $salesReturnComponent->quantity = $request->input('quantity');
        $salesReturnComponent->price = $request->input('price');
        $salesReturnComponent->total = $request->input('total');
        $salesReturnComponent->save();
    }
}
