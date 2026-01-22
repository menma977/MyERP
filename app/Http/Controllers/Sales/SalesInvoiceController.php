<?php

namespace App\Http\Controllers\Sales;

use App\Enums\DiscountTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Sales\SalesInvoice;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesInvoiceController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, SalesInvoice>|Collection<int, SalesInvoice>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $salesInvoices = SalesInvoice::with([
            'components',
        ])->when($request->input('search'), function ($query) use ($request) {
            $query->where('code', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $salesInvoices->get();
        }

        return $salesInvoices->withContributors()->withUsers()->paginate($request->input('per_page', 10));
    }

    /**
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'sales_order_id' => ['required', 'exists:sales_orders,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', Rule::enum(DiscountTypeEnum::class)],
            'discount' => ['required', 'numeric', 'min:0'],
            'fee' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $salesInvoice = new SalesInvoice;
        $salesInvoice->code = CodeGeneratorService::code('SI')->number(SalesInvoice::count() + 1)->generate();
        $salesInvoice->sales_order_id = $request->input('sales_order_id');
        $this->save($request, $salesInvoice);

        return ['message' => trans('messages.success.store', ['target' => 'Sales Invoice'])];
    }

    public function show(Request $request): SalesInvoice
    {
        return SalesInvoice::with('components')->withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', Rule::enum(DiscountTypeEnum::class)],
            'discount' => ['required', 'numeric', 'min:0'],
            'fee' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = SalesInvoice::findOrFail($request->route('id'));
        $this->save($request, $salesInvoice);

        return ['message' => trans('messages.success.update', ['target' => 'Sales Invoice'])];
    }

    /**
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = SalesInvoice::findOrFail($request->route('id'));
        $salesInvoice->delete();

        return ['message' => trans('messages.success.delete', ['target' => 'Sales Invoice'])];
    }

    /**
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = SalesInvoice::onlyTrashed()->findOrFail($request->route('id'));
        $salesInvoice->restore();

        return ['message' => trans('messages.success.restore', ['target' => 'Sales Invoice'])];
    }

    /**
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = SalesInvoice::onlyTrashed()->findOrFail($request->route('id'));
        $salesInvoice->forceDelete();

        return ['message' => trans('messages.success.destroy', ['target' => 'Sales Invoice'])];
    }

    /**
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = SalesInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Sales Invoice', 'target' => 'Access']),
            ]);
        }

        $salesInvoice->approve($user);

        return ['message' => trans('messages.success.approve', ['target' => 'Sales Invoice'])];
    }

    /**
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = SalesInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Sales Invoice', 'target' => 'Access']),
            ]);
        }

        $salesInvoice->reject($user);

        return ['message' => trans('messages.success.reject', ['target' => 'Sales Invoice'])];
    }

    protected function save(Request $request, SalesInvoice $salesInvoice): void
    {
        $salesInvoice->total = $request->input('total');
        $salesInvoice->tax = $request->input('tax');
        $salesInvoice->discount_type = $request->input('discount_type');
        $salesInvoice->discount = $request->input('discount');
        $salesInvoice->fee = $request->input('fee');

        $discountAmount = $request->input('discount');
        if ($request->input('discount_type') === DiscountTypeEnum::PERCENT->value) {
            $discountAmount = ($request->input('total') * $request->input('discount')) / 100;
        }

        $salesInvoice->grand_total = (float) ($request->input('total') + $request->input('tax') + $request->input('fee') - $discountAmount);
        $salesInvoice->note = $request->input('note');
        $salesInvoice->save();
    }
}
