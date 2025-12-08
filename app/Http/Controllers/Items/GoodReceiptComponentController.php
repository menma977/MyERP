<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\GoodReceiptComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GoodReceiptComponentController extends Controller
{
    /**
     * Good Receipt Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, GoodReceiptComponent>|Collection<int, GoodReceiptComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $goodReceiptComponents = GoodReceiptComponent::with([
            'goodReceipt',
            'item',
            'purchaseOrderComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('item', function (Builder $query) use ($request) {
                    return $query->where('name', 'like', '%' . $request->input('search') . '%');
                })->orWhereHas('goodReceipt', function (Builder $query) use ($request) {
                    return $query->where('code', 'like', '%' . $request->input('search') . '%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $goodReceiptComponents->get();
        }

        return $goodReceiptComponents->paginate($request->input('per_page', 10));
    }

    /**
     * Good Receipt Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'good_receipt_id' => ['required', 'string', 'exists:good_receipts,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        $goodReceiptComponent = new GoodReceiptComponent;
        $goodReceiptComponent->good_receipt_id = $request->input('good_receipt_id');
        $goodReceiptComponent->purchase_order_component_id = $request->input('purchase_order_component_id');
        $goodReceiptComponent->item_id = $request->input('item_id');
        $goodReceiptComponent->quantity = $request->input('quantity');
        $goodReceiptComponent->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Good Receipt Component']),
        ];
    }

    /**
     * Good Receipt Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): GoodReceiptComponent
    {
        return GoodReceiptComponent::with([
            'goodReceipt',
            'item',
            'purchaseOrderComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Good Receipt Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'good_receipt_id' => ['required', 'string', 'exists:good_receipts,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var GoodReceiptComponent $goodReceiptComponent */
        $goodReceiptComponent = GoodReceiptComponent::findOrFail($request->route('id'));
        $goodReceiptComponent->good_receipt_id = $request->input('good_receipt_id');
        $goodReceiptComponent->purchase_order_component_id = $request->input('purchase_order_component_id');
        $goodReceiptComponent->item_id = $request->input('item_id');
        $goodReceiptComponent->quantity = $request->input('quantity');
        $goodReceiptComponent->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Good Receipt Component']),
        ];
    }

    /**
     * Good Receipt Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var GoodReceiptComponent $goodReceiptComponent */
        $goodReceiptComponent = GoodReceiptComponent::findOrFail($request->route('id'));
        $goodReceiptComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Good Receipt Component']),
        ];
    }

    /**
     * Good Receipt Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var GoodReceiptComponent $goodReceiptComponent */
        $goodReceiptComponent = GoodReceiptComponent::onlyTrashed()->findOrFail($request->route('id'));
        $goodReceiptComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Good Receipt Component']),
        ];
    }

    /**
     * Good Receipt Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var GoodReceiptComponent $goodReceiptComponent */
        $goodReceiptComponent = GoodReceiptComponent::onlyTrashed()->findOrFail($request->route('id'));
        $goodReceiptComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Good Receipt Component']),
        ];
    }
}
