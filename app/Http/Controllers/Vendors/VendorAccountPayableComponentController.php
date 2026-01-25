<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendors\VendorAccountPayableComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class VendorAccountPayableComponentController extends Controller
{
    /**
     * Vendor Account Payable Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorAccountPayableComponent>|Collection<int, VendorAccountPayableComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $vendorAccountPayableComponents = VendorAccountPayableComponent::with([
            'accountPayable',
            'purchaseInvoiceComponent',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('accountPayable', function (Builder $query) use ($request) {
                    $query->where('amount', 'like', '%'.$request->input('search').'%');
                });
            });
        })->where('vendor_account_payable_id', $request->route('vendor_account_payable_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $vendorAccountPayableComponents->get();
        }

        return $vendorAccountPayableComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Vendor Account Payable Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): VendorAccountPayableComponent
    {
        return VendorAccountPayableComponent::with([
            'accountPayable',
            'purchaseInvoiceComponent',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Vendor Account Payable Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_account_payable_id' => ['required', 'string', 'exists:vendor_account_payables,id'],
            'purchase_invoice_component_id' => ['required', 'string', 'exists:purchase_invoice_components,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $vendorAccountPayableComponent = new VendorAccountPayableComponent;
        $this->save($request, $vendorAccountPayableComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Account Payable Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Account Payable Component Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_account_payable_id' => ['required', 'string', 'exists:vendor_account_payables,id'],
            'purchase_invoice_component_id' => ['required', 'string', 'exists:purchase_invoice_components,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var VendorAccountPayableComponent $vendorAccountPayableComponent */
        $vendorAccountPayableComponent = VendorAccountPayableComponent::findOrFail($request->route('id'));
        $this->save($request, $vendorAccountPayableComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Account Payable Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Account Payable Component Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var VendorAccountPayableComponent $vendorAccountPayableComponent */
        $vendorAccountPayableComponent = VendorAccountPayableComponent::findOrFail($request->route('id'));
        $vendorAccountPayableComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Account Payable Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Account Payable Component Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var VendorAccountPayableComponent $vendorAccountPayableComponent */
        $vendorAccountPayableComponent = VendorAccountPayableComponent::onlyTrashed()->findOrFail($request->route('id'));
        $vendorAccountPayableComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Account Payable Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Account Payable Component Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorAccountPayableComponent $vendorAccountPayableComponent */
        $vendorAccountPayableComponent = VendorAccountPayableComponent::onlyTrashed()->findOrFail($request->route('id'));
        $vendorAccountPayableComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Account Payable Component'], App::getLocale()),
        ];
    }

    protected function save(Request $request, VendorAccountPayableComponent $vendorAccountPayableComponent): void
    {
        $vendorAccountPayableComponent->vendor_account_payable_id = $request->input('vendor_account_payable_id');
        $vendorAccountPayableComponent->purchase_invoice_component_id = $request->input('purchase_invoice_component_id');
        $vendorAccountPayableComponent->quantity = $request->input('quantity');
        $vendorAccountPayableComponent->price = $request->input('price');
        $vendorAccountPayableComponent->total = $vendorAccountPayableComponent->quantity * $vendorAccountPayableComponent->price;
        $vendorAccountPayableComponent->save();
    }
}
