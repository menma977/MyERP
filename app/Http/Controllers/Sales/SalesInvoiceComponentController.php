<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\SalesInvoiceComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesInvoiceComponentController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, SalesInvoiceComponent>|Collection<int, SalesInvoiceComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $salesInvoiceComponents = SalesInvoiceComponent::with([
            'invoice',
            'item',
        ])->when($request->input('search'), function ($query) use ($request) {
            $query->whereHas('item', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $salesInvoiceComponents->get();
        }

        return $salesInvoiceComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'sales_invoice_id' => ['required', 'ulid', 'exists:sales_invoices,id'],
            'item_id' => ['required', 'ulid', 'exists:items,id'],
            'item_batch_id' => ['nullable', 'ulid', 'exists:item_batches,id'],
            'item_stock_id' => ['nullable', 'ulid', 'exists:item_stocks,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $salesInvoiceComponent = new SalesInvoiceComponent;
        $this->save($request, $salesInvoiceComponent);

        return ['message' => trans('messages.success.store', ['target' => 'Sales Invoice Component'])];
    }

    public function show(Request $request): SalesInvoiceComponent
    {
        return SalesInvoiceComponent::with([
            'invoice',
            'item',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'sales_invoice_id' => ['required', 'ulid', 'exists:sales_invoices,id'],
            'item_id' => ['required', 'ulid', 'exists:items,id'],
            'item_batch_id' => ['nullable', 'ulid', 'exists:item_batches,id'],
            'item_stock_id' => ['nullable', 'ulid', 'exists:item_stocks,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var SalesInvoiceComponent $salesInvoiceComponent */
        $salesInvoiceComponent = SalesInvoiceComponent::findOrFail($request->route('id'));
        $this->save($request, $salesInvoiceComponent);

        return ['message' => trans('messages.success.update', ['target' => 'Sales Invoice Component'])];
    }

    /**
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var SalesInvoiceComponent $salesInvoiceComponent */
        $salesInvoiceComponent = SalesInvoiceComponent::findOrFail($request->route('id'));
        $salesInvoiceComponent->delete();

        return ['message' => trans('messages.success.delete', ['target' => 'Sales Invoice Component'])];
    }

    /**
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var SalesInvoiceComponent $salesInvoiceComponent */
        $salesInvoiceComponent = SalesInvoiceComponent::onlyTrashed()->findOrFail($request->route('id'));
        $salesInvoiceComponent->restore();

        return ['message' => trans('messages.success.restore', ['target' => 'Sales Invoice Component'])];
    }

    /**
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesInvoiceComponent $salesInvoiceComponent */
        $salesInvoiceComponent = SalesInvoiceComponent::onlyTrashed()->findOrFail($request->route('id'));
        $salesInvoiceComponent->forceDelete();

        return ['message' => trans('messages.success.destroy', ['target' => 'Sales Invoice Component'])];
    }

    protected function save(Request $request, SalesInvoiceComponent $salesInvoiceComponent): void
    {
        $salesInvoiceComponent->sales_invoice_id = $request->input('sales_invoice_id');
        /** @noinspection DuplicatedCode */
        $salesInvoiceComponent->item_id = $request->input('item_id');
        $salesInvoiceComponent->item_batch_id = $request->input('item_batch_id');
        $salesInvoiceComponent->item_stock_id = $request->input('item_stock_id');
        $salesInvoiceComponent->quantity = $request->input('quantity');
        $salesInvoiceComponent->price = $request->input('price');
        $salesInvoiceComponent->total = $request->input('total');
        $salesInvoiceComponent->save();
    }
}
