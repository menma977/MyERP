<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PurchaseProcurementComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseProcurementComponentController extends Controller
{
    /**
     * Purchase Procurement Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseProcurementComponent>|Collection<int, PurchaseProcurementComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $purchaseProcurementComponents = PurchaseProcurementComponent::with([
            'procurement',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query->where('note', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $purchaseProcurementComponents->get();
        }

        return $purchaseProcurementComponents->paginate($request->input('per_page', 10));
    }

    /**
     * Purchase Procurement Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'purchase_procurement_id' => ['required', 'string', 'exists:purchase_procurements,id'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Purchase Procurement Component', 'target' => 'Access']),
            ]);
        }

        $purchaseProcurementComponent = new PurchaseProcurementComponent;
        $this->save($request, $purchaseProcurementComponent);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Procurement Component']),
        ];
    }

    /**
     * Purchase Procurement Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): PurchaseProcurementComponent
    {
        return PurchaseProcurementComponent::with([
            'procurement',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Purchase Procurement Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_procurement_id' => ['required', 'string', 'exists:purchase_procurements,id'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var PurchaseProcurementComponent $purchaseProcurementComponent */
        $purchaseProcurementComponent = PurchaseProcurementComponent::findOrFail($request->route('id'));
        $this->save($request, $purchaseProcurementComponent);

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Procurement Component']),
        ];
    }

    /**
     * Purchase Procurement Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseProcurementComponent $purchaseProcurementComponent */
        $purchaseProcurementComponent = PurchaseProcurementComponent::findOrFail($request->route('id'));
        $purchaseProcurementComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Procurement Component']),
        ];
    }

    /**
     * Purchase Procurement Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseProcurementComponent $purchaseProcurementComponent */
        $purchaseProcurementComponent = PurchaseProcurementComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseProcurementComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Procurement Component']),
        ];
    }

    /**
     * Purchase Procurement Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseProcurementComponent $purchaseProcurementComponent */
        $purchaseProcurementComponent = PurchaseProcurementComponent::onlyTrashed()->findOrFail($request->route('id'));
        $purchaseProcurementComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Procurement Component']),
        ];
    }

    /**
     * Save purchase procurement component.
     */
    protected function save(Request $request, PurchaseProcurementComponent $purchaseProcurementComponent): void
    {
        $purchaseProcurementComponent->purchase_procurement_id = $request->input('purchase_procurement_id');
        $purchaseProcurementComponent->note = $request->input('note');
        $purchaseProcurementComponent->save();
    }
}
