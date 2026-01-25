<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendors\VendorInvoiceComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class VendorInvoiceComponentController extends Controller
{
    /**
     * Vendor Invoice Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorInvoiceComponent>|Collection<int, VendorInvoiceComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $vendorInvoiceComponents = VendorInvoiceComponent::with([
            'invoice',
            'vendorComponent',
            'item',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('invoice', function (Builder $query) use ($request) {
                    $query->where('code', 'like', '%'.$request->input('search').'%');
                });
            });
        })->where('vendor_invoice_id', $request->route('vendor_invoice_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $vendorInvoiceComponents->get();
        }

        return $vendorInvoiceComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Vendor Invoice Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): VendorInvoiceComponent
    {
        return VendorInvoiceComponent::with([
            'invoice',
            'vendorComponent',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Vendor Invoice Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_invoice_id' => ['required', 'string', 'exists:vendor_invoices,id'],
            'vendor_component_id' => ['required', 'string', 'exists:vendor_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $vendorInvoiceComponent = new VendorInvoiceComponent;
        $this->save($request, $vendorInvoiceComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Invoice Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Component Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_invoice_id' => ['required', 'string', 'exists:vendor_invoices,id'],
            'vendor_component_id' => ['required', 'string', 'exists:vendor_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var VendorInvoiceComponent $vendorInvoiceComponent */
        $vendorInvoiceComponent = VendorInvoiceComponent::findOrFail($request->route('id'));
        $this->save($request, $vendorInvoiceComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Invoice Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Component Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var VendorInvoiceComponent $vendorInvoiceComponent */
        $vendorInvoiceComponent = VendorInvoiceComponent::findOrFail($request->route('id'));
        $vendorInvoiceComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Invoice Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Component Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var VendorInvoiceComponent $vendorInvoiceComponent */
        $vendorInvoiceComponent = VendorInvoiceComponent::onlyTrashed()->findOrFail($request->route('id'));
        $vendorInvoiceComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Invoice Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Component Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorInvoiceComponent $vendorInvoiceComponent */
        $vendorInvoiceComponent = VendorInvoiceComponent::onlyTrashed()->findOrFail($request->route('id'));
        $vendorInvoiceComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Invoice Component'], App::getLocale()),
        ];
    }

    protected function save(Request $request, VendorInvoiceComponent $vendorInvoiceComponent): void
    {
        $vendorInvoiceComponent->vendor_invoice_id = $request->input('vendor_invoice_id');
        $vendorInvoiceComponent->vendor_component_id = $request->input('vendor_component_id');
        $vendorInvoiceComponent->item_id = $request->input('item_id');
        $vendorInvoiceComponent->quantity = $request->input('quantity');
        $vendorInvoiceComponent->price = $request->input('price');
        $vendorInvoiceComponent->total = $vendorInvoiceComponent->quantity * $vendorInvoiceComponent->price;
        $vendorInvoiceComponent->save();
    }
}
