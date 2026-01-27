<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\Purchases\PurchaseRequestComponentResource;
use App\Models\Purchases\PurchaseRequest;
use App\Models\Purchases\PurchaseRequestComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseRequestComponentController extends Controller
{
    /**
     * Purchase Request Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseRequestComponent>|Collection<int, PurchaseRequestComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $purchaseRequestComponents = PurchaseRequestComponent::with([
            'request',
            'vendor',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('note', 'like', '%'.$request->input('search').'%')->orWhereHas('vendor', function (Builder $query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('search').'%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return PurchaseRequestComponentResource::collection($purchaseRequestComponents->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $purchaseRequestComponents->count();
        }

        return PurchaseRequestComponentResource::collection($purchaseRequestComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Purchase Request Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, purchase_request_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'purchase_request_id' => ['required', 'string', 'exists:purchase_requests,id'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Purchase Request Component', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequestComponent = new PurchaseRequestComponent;
        $this->save($request, $purchaseRequestComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Request Component'], App::getLocale()),
            'purchase_request_component' => $purchaseRequestComponent->toResource(),
        ];
    }

    /**
     * Purchase Request Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return PurchaseRequestComponent::with([
            'request',
            'vendor',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Purchase Request Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, purchase_request_component: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_request_id' => ['required', 'string', 'exists:purchase_requests,id'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var PurchaseRequestComponent $purchaseRequestComponent */
        $purchaseRequestComponent = PurchaseRequestComponent::where('id', $request->route('id'))->firstOrFail();
        $this->save($request, $purchaseRequestComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Request Component'], App::getLocale()),
            'purchase_request_component' => $purchaseRequestComponent->toResource(),
        ];
    }

    /**
     * Purchase Request Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, purchase_request_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseRequestComponent $purchaseRequestComponent */
        $purchaseRequestComponent = PurchaseRequestComponent::where('id', $request->route('id'))->firstOrFail();
        $purchaseRequestComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Request Component'], App::getLocale()),
            'purchase_request_component' => $purchaseRequestComponent->toResource(),
        ];
    }

    /**
     * Purchase Request Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, purchase_request_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseRequestComponent $purchaseRequestComponent */
        $purchaseRequestComponent = PurchaseRequestComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseRequestComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Request Component'], App::getLocale()),
            'purchase_request_component' => $purchaseRequestComponent->toResource(),
        ];
    }

    /**
     * Purchase Request Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, purchase_request_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseRequestComponent $purchaseRequestComponent */
        $purchaseRequestComponent = PurchaseRequestComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseRequestComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Request Component'], App::getLocale()),
            'purchase_request_component' => $purchaseRequestComponent->toResource(),
        ];
    }

    /**
     * Save the purchase request component and update the total amount in the purchase request.
     */
    protected function save(Request $request, PurchaseRequestComponent $purchaseRequestComponent): void
    {
        $purchaseRequestComponent->purchase_request_id = $request->input('purchase_request_id');
        $purchaseRequestComponent->vendor_id = $request->input('vendor_id');
        $purchaseRequestComponent->item_id = $request->input('item_id');
        $purchaseRequestComponent->price = $request->integer('price');
        $purchaseRequestComponent->quantity = $request->float('quantity');
        $purchaseRequestComponent->total = $purchaseRequestComponent->price * $purchaseRequestComponent->quantity;
        $purchaseRequestComponent->note = $request->input('note');
        $purchaseRequestComponent->save();

        $purchaseRequest = PurchaseRequest::find($purchaseRequestComponent->purchase_request_id);
        if (! $purchaseRequest) {
            return;
        }

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest->total = (float) $purchaseRequest->components()->sum('total');
        $purchaseRequest->save();
    }
}
