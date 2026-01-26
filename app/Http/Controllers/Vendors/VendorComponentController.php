<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vendors\VendorComponentResource;
use App\Models\Vendors\VendorComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class VendorComponentController extends Controller
{
    /**
     * Vendor Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorComponent>|Collection<int, VendorComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $vendorComponents = VendorComponent::with([
            'vendor',
            'item',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('vendor', function (Builder $query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('search').'%');
                })->orWhereHas('item', function (Builder $query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('search').'%')
                        ->orWhere('code', 'like', '%'.$request->input('search').'%');
                });
            });
        })->where('vendor_id', $request->route('vendor_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return VendorComponentResource::collection($vendorComponents->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $vendorComponents->count();
        }

        return VendorComponentResource::collection($vendorComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Vendor Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return VendorComponent::with([
            'vendor',
            'item',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Vendor Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, vendor_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $vendorComponent = new VendorComponent;
        $vendorComponent->vendor_id = $request->input('vendor_id');
        $vendorComponent->item_id = $request->input('item_id');
        $vendorComponent->price = $request->input('price');
        $vendorComponent->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Component'], App::getLocale()),
            'vendor_component' => $vendorComponent->toResource(),
        ];
    }

    /**
     * Vendor Component Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string, vendor_component: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var VendorComponent $vendorComponent */
        $vendorComponent = VendorComponent::where('id', $request->route('id'))->firstOrFail();
        $vendorComponent->vendor_id = $request->input('vendor_id');
        $vendorComponent->item_id = $request->input('item_id');
        $vendorComponent->price = $request->input('price');
        $vendorComponent->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Component'], App::getLocale()),
            'vendor_component' => $vendorComponent->toResource(),
        ];
    }

    /**
     * Vendor Component Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string, vendor_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var VendorComponent $vendorComponent */
        $vendorComponent = VendorComponent::where('id', $request->route('id'))->firstOrFail();
        $vendorComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Component'], App::getLocale()),
            'vendor_component' => $vendorComponent->toResource(),
        ];
    }

    /**
     * Vendor Component Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string, vendor_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var VendorComponent $vendorComponent */
        $vendorComponent = VendorComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $vendorComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Component'], App::getLocale()),
            'vendor_component' => $vendorComponent->toResource(),
        ];
    }

    /**
     * Vendor Component Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string, vendor_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorComponent $vendorComponent */
        $vendorComponent = VendorComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $vendorComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Component'], App::getLocale()),
            'vendor_component' => $vendorComponent->toResource(),
        ];
    }
}
