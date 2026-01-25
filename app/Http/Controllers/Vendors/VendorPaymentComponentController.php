<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendors\VendorPaymentComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class VendorPaymentComponentController extends Controller
{
    /**
     * Vendor Payment Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorPaymentComponent>|Collection<int, VendorPaymentComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $vendorPaymentComponents = VendorPaymentComponent::with([
            'payment',
            'accountPayableComponent',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('payment', function (Builder $query) use ($request) {
                    $query->where('amount', 'like', '%'.$request->input('search').'%');
                });
            });
        })->where('vendor_payment_id', $request->route('vendor_payment_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $vendorPaymentComponents->get();
        }

        return $vendorPaymentComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Vendor Payment Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): VendorPaymentComponent
    {
        return VendorPaymentComponent::with([
            'payment',
            'accountPayableComponent',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Vendor Payment Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_payment_id' => ['required', 'string', 'exists:vendor_payments,id'],
            'vendor_account_payable_component_id' => ['required', 'string', 'exists:vendor_account_payable_components,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $vendorPaymentComponent = new VendorPaymentComponent;
        $this->save($request, $vendorPaymentComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Payment Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Payment Component Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_payment_id' => ['required', 'string', 'exists:vendor_payments,id'],
            'vendor_account_payable_component_id' => ['required', 'string', 'exists:vendor_account_payable_components,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var VendorPaymentComponent $vendorPaymentComponent */
        $vendorPaymentComponent = VendorPaymentComponent::findOrFail($request->route('id'));
        $this->save($request, $vendorPaymentComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Payment Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Payment Component Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var VendorPaymentComponent $vendorPaymentComponent */
        $vendorPaymentComponent = VendorPaymentComponent::findOrFail($request->route('id'));
        $vendorPaymentComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Payment Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Payment Component Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var VendorPaymentComponent $vendorPaymentComponent */
        $vendorPaymentComponent = VendorPaymentComponent::onlyTrashed()->findOrFail($request->route('id'));
        $vendorPaymentComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Payment Component'], App::getLocale()),
        ];
    }

    /**
     * Vendor Payment Component Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorPaymentComponent $vendorPaymentComponent */
        $vendorPaymentComponent = VendorPaymentComponent::onlyTrashed()->findOrFail($request->route('id'));
        $vendorPaymentComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Payment Component'], App::getLocale()),
        ];
    }

    protected function save(Request $request, VendorPaymentComponent $vendorPaymentComponent): void
    {
        $vendorPaymentComponent->vendor_payment_id = $request->input('vendor_payment_id');
        $vendorPaymentComponent->vendor_account_payable_component_id = $request->input('vendor_account_payable_component_id');
        $vendorPaymentComponent->quantity = $request->input('quantity');
        $vendorPaymentComponent->price = $request->input('price');
        $vendorPaymentComponent->total = $vendorPaymentComponent->quantity * $vendorPaymentComponent->price;
        $vendorPaymentComponent->save();
    }
}
