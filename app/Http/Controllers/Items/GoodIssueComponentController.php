<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Resources\Items\GoodIssueComponentResource;
use App\Models\Items\GoodIssueComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class GoodIssueComponentController extends Controller
{
    /**
     * Good Issue Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, GoodIssueComponent>|Collection<int, GoodIssueComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $goodIssueComponents = GoodIssueComponent::with([
            'good',
            'item',
            'batch',
            'stock',
            'salesInvoiceComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->whereHas('item', function (Builder $query) use ($request) {
                    return $query->where('name', 'like', '%'.$request->input('search').'%');
                })->orWhereHas('good', function (Builder $query) use ($request) {
                    return $query->where('code', 'like', '%'.$request->input('search').'%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return GoodIssueComponentResource::collection($goodIssueComponents->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $goodIssueComponents->count();
        }

        return GoodIssueComponentResource::collection($goodIssueComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Good Issue Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, good_issue_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'good_issue_id' => ['required', 'string', 'exists:good_issues,id'],
            'sales_invoice_component_id' => ['required', 'string', 'exists:sales_invoice_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'item_batch_id' => ['required', 'string', 'exists:item_batches,id'],
            'item_stock_id' => ['required', 'string', 'exists:item_stocks,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        $goodIssueComponent = new GoodIssueComponent;
        $goodIssueComponent->good_issue_id = $request->input('good_issue_id');
        $goodIssueComponent->sales_invoice_component_id = $request->input('sales_invoice_component_id');
        $goodIssueComponent->item_id = $request->input('item_id');
        $goodIssueComponent->item_batch_id = $request->input('item_batch_id');
        $goodIssueComponent->item_stock_id = $request->input('item_stock_id');
        $goodIssueComponent->quantity = $request->input('quantity');
        $goodIssueComponent->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Good Issue Component'], App::getLocale()),
            'good_issue_component' => $goodIssueComponent->toResource(),
        ];
    }

    /**
     * Good Issue Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return GoodIssueComponent::with([
            'good',
            'item',
            'batch',
            'stock',
            'salesInvoiceComponent',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Good Issue Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, good_issue_component: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'good_issue_id' => ['required', 'string', 'exists:good_issues,id'],
            'sales_invoice_component_id' => ['required', 'string', 'exists:sales_invoice_components,id'],
            'item_id' => ['required', 'string', 'exists:items,id'],
            'item_batch_id' => ['required', 'string', 'exists:item_batches,id'],
            'item_stock_id' => ['required', 'string', 'exists:item_stocks,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var GoodIssueComponent $goodIssueComponent */
        $goodIssueComponent = GoodIssueComponent::where('id', $request->route('id'))->firstOrFail();
        $goodIssueComponent->good_issue_id = $request->input('good_issue_id');
        $goodIssueComponent->sales_invoice_component_id = $request->input('sales_invoice_component_id');
        $goodIssueComponent->item_id = $request->input('item_id');
        $goodIssueComponent->item_batch_id = $request->input('item_batch_id');
        $goodIssueComponent->item_stock_id = $request->input('item_stock_id');
        $goodIssueComponent->quantity = $request->input('quantity');
        $goodIssueComponent->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Good Issue Component'], App::getLocale()),
            'good_issue_component' => $goodIssueComponent->toResource(),
        ];
    }

    /**
     * Good Issue Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, good_issue_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var GoodIssueComponent $goodIssueComponent */
        $goodIssueComponent = GoodIssueComponent::where('id', $request->route('id'))->firstOrFail();
        $goodIssueComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Good Issue Component'], App::getLocale()),
            'good_issue_component' => $goodIssueComponent->toResource(),
        ];
    }

    /**
     * Good Issue Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, good_issue_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var GoodIssueComponent $goodIssueComponent */
        $goodIssueComponent = GoodIssueComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $goodIssueComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Good Issue Component'], App::getLocale()),
            'good_issue_component' => $goodIssueComponent->toResource(),
        ];
    }

    /**
     * Good Issue Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, good_issue_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var GoodIssueComponent $goodIssueComponent */
        $goodIssueComponent = GoodIssueComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $goodIssueComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Good Issue Component'], App::getLocale()),
            'good_issue_component' => $goodIssueComponent->toResource(),
        ];
    }
}
