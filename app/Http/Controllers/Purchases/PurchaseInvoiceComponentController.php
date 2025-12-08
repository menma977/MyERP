<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseInvoiceComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Purchase Invoice Component Controller
 *
 * Handles CRUD operations for Purchase Invoice Components.
 */
class PurchaseInvoiceComponentController extends Controller
{
    /**
     * Purchase Invoice Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, PurchaseInvoiceComponent>|Collection<int, PurchaseInvoiceComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseInvoiceComponents = PurchaseInvoiceComponent::with([
            'invoice',
            'orderComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('invoice', function (Builder $query) use ($request) {
                    $query->where('code', 'like', '%'.$request->input('search').'%');
                })->orWhereHas('orderComponent', function (Builder $query) use ($request) {
                    $query->where('item_id', 'like', '%'.$request->input('search').'%');
                });
            });
        })->where('purchase_invoice_id', $request->route('purchase_invoice_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseInvoiceComponents->get();
        }

        return $purchaseInvoiceComponents->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Invoice Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): PurchaseInvoiceComponent
    {
        return PurchaseInvoiceComponent::with([
            'invoice',
            'orderComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Invoice Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'purchase_invoice_id' => ['required', 'string', 'exists:purchase_invoices,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $purchaseInvoiceComponent = new PurchaseInvoiceComponent;
        $this->save($request, $purchaseInvoiceComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Invoice Component']),
        ];
    }

    /**
     * Purchase Invoice Component Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_invoice_id' => ['required', 'string', 'exists:purchase_invoices,id'],
            'purchase_order_component_id' => ['required', 'string', 'exists:purchase_order_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var PurchaseInvoiceComponent $purchaseInvoiceComponent */
        $purchaseInvoiceComponent = PurchaseInvoiceComponent::findOrFail($request->route('id'));
        $this->save($request, $purchaseInvoiceComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Invoice Component']),
        ];
    }

    /**
     * Purchase Invoice Component Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseInvoiceComponent $purchaseInvoiceComponent */
        $purchaseInvoiceComponent = PurchaseInvoiceComponent::findOrFail($request->route('id'));
        $purchaseInvoiceComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Invoice Component']),
        ];
    }

    /**
     * Purchase Invoice Component Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseInvoiceComponent $purchaseInvoiceComponent */
        $purchaseInvoiceComponent = PurchaseInvoiceComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseInvoiceComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Invoice Component']),
        ];
    }

    /**
     * Purchase Invoice Component Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseInvoiceComponent $purchaseInvoiceComponent */
        $purchaseInvoiceComponent = PurchaseInvoiceComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseInvoiceComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Invoice Component']),
        ];
    }

    protected function save(Request $request, PurchaseInvoiceComponent $purchaseInvoiceComponent): void
    {
        $purchaseInvoiceComponent->purchase_invoice_id = $request->input('purchase_invoice_id');
        $purchaseInvoiceComponent->purchase_order_component_id = $request->input('purchase_order_component_id');
        $purchaseInvoiceComponent->item_id = $request->input('item_id');
        $purchaseInvoiceComponent->quantity = $request->input('quantity');
        $purchaseInvoiceComponent->price = $request->input('price');
        $purchaseInvoiceComponent->total = $purchaseInvoiceComponent->price * $purchaseInvoiceComponent->quantity;
        $purchaseInvoiceComponent->save();

        $purchaseInvoice = PurchaseInvoice::find($purchaseInvoiceComponent->purchase_invoice_id);
        if (! $purchaseInvoice) {
            return;
        }

        /** @var PurchaseInvoice $purchaseInvoice */
        $purchaseInvoice->total = (float) $purchaseInvoice->components()->sum('total');
        $purchaseInvoice->save();
    }
}
