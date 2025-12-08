<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseReturn;
use App\Models\Purchases\PurchaseReturnComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseReturnComponentController extends Controller
{
    /**
     * Purchase Return Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseReturnComponent>|Collection<int, PurchaseReturnComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseReturnComponents = PurchaseReturnComponent::with([
            'return',
            'purchaseOrderComponent',
            'goodReceiptComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('note', 'like', '%'.$request->input('search').'%')->orWhereHas('goodReceiptComponent', function (Builder $query) use ($request) {
                    $query->where('note', 'like', '%'.$request->input('search').'%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseReturnComponents->get();
        }

        return $purchaseReturnComponents->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Return Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $this->validate($request);

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Purchase Return Component', 'target' => 'Access']),
            ]);
        }

        $purchaseReturnComponent = new PurchaseReturnComponent;
        $this->save($request, $purchaseReturnComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Return Component']),
        ];
    }

    /**
     * Purchase Return Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): PurchaseReturnComponent
    {
        return PurchaseReturnComponent::with([
            'return',
            'purchaseOrderComponent',
            'goodReceiptComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Return Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $this->validate($request);

        /** @var PurchaseReturnComponent $purchaseReturnComponent */
        $purchaseReturnComponent = PurchaseReturnComponent::findOrFail($request->route('id'));
        $this->save($request, $purchaseReturnComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Return Component']),
        ];
    }

    /**
     * Purchase Return Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseReturnComponent $purchaseReturnComponent */
        $purchaseReturnComponent = PurchaseReturnComponent::findOrFail($request->route('id'));
        $purchaseReturnComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Return Component']),
        ];
    }

    /**
     * Purchase Return Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseReturnComponent $purchaseReturnComponent */
        $purchaseReturnComponent = PurchaseReturnComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseReturnComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Return Component']),
        ];
    }

    /**
     * Purchase Return Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseReturnComponent $purchaseReturnComponent */
        $purchaseReturnComponent = PurchaseReturnComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseReturnComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Return Component']),
        ];
    }

    /**
     * Save the purchase return component and update the total amount in purchase return.
     */
    protected function save(Request $request, PurchaseReturnComponent $purchaseReturnComponent): void
    {
        $purchaseReturnComponent->purchase_return_id = $request->input('purchase_return_id');
        $purchaseReturnComponent->purchase_order_component_id = $request->input('purchase_order_component_id');
        $purchaseReturnComponent->good_receipt_component_id = $request->input('good_receipt_component_id');
        $purchaseReturnComponent->item_id = $request->input('item_id');
        $purchaseReturnComponent->quantity = $request->input('quantity');
        $purchaseReturnComponent->price = $request->input('price');
        $purchaseReturnComponent->total = $request->input('total');
        $purchaseReturnComponent->note = $request->input('note');
        $purchaseReturnComponent->save();

        $purchaseReturn = PurchaseReturn::find($purchaseReturnComponent->purchase_return_id);
        if (! $purchaseReturn) {
            return;
        }

        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn->total = (float) $purchaseReturn->components()->sum('total');
        $purchaseReturn->save();
    }

    protected function validate(Request $request): void
    {
        $request->validate([
            'purchase_return_id' => ['required', 'string', 'exists:purchase_returns,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'good_receipt_component_id' => ['required', 'string', 'exists:good_receipt_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
