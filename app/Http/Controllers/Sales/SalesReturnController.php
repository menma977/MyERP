<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\SalesReturn;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalesReturnController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, SalesReturn>|Collection<int, SalesReturn>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $salesReturns = SalesReturn::with([
            'components',
        ])->when($request->input('search'), function ($query) use ($request) {
            $query->where('code', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $salesReturns->get();
        }

        return $salesReturns->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'sales_order_id' => ['required', 'exists:sales_orders,id'],
            'sales_invoice_id' => ['required', 'exists:sales_invoices,id'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $salesReturn = new SalesReturn;
        $salesReturn->code = CodeGeneratorService::code('SR')->number(SalesReturn::count() + 1)->generate();
        $salesReturn->sales_order_id = $request->input('sales_order_id');
        $salesReturn->sales_invoice_id = $request->input('sales_invoice_id');
        $salesReturn->total = $request->input('total');
        $salesReturn->save();

        return ['message' => trans('messages.success.store', ['target' => 'Sales Return'])];
    }

    public function show(Request $request): SalesReturn
    {
        return SalesReturn::with([
            'components',
        ])->withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var SalesReturn $salesReturn */
        $salesReturn = SalesReturn::findOrFail($request->route('id'));
        $salesReturn->total = $request->input('total');
        $salesReturn->save();

        return ['message' => trans('messages.success.update', ['target' => 'Sales Return'])];
    }

    /**
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var SalesReturn $salesReturn */
        $salesReturn = SalesReturn::findOrFail($request->route('id'));
        $salesReturn->delete();

        return ['message' => trans('messages.success.delete', ['target' => 'Sales Return'])];
    }

    /**
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var SalesReturn $salesReturn */
        $salesReturn = SalesReturn::onlyTrashed()->findOrFail($request->route('id'));
        $salesReturn->restore();

        return ['message' => trans('messages.success.restore', ['target' => 'Sales Return'])];
    }

    /**
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesReturn $salesReturn */
        $salesReturn = SalesReturn::onlyTrashed()->findOrFail($request->route('id'));
        $salesReturn->forceDelete();

        return ['message' => trans('messages.success.destroy', ['target' => 'Sales Return'])];
    }

    /**
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var SalesReturn $salesReturn */
        $salesReturn = SalesReturn::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Sales Return', 'target' => 'Access']),
            ]);
        }

        $salesReturn->approve($user);

        return ['message' => trans('messages.success.approve', ['target' => 'Sales Return'])];
    }

    /**
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var SalesReturn $salesReturn */
        $salesReturn = SalesReturn::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Sales Return', 'target' => 'Access']),
            ]);
        }

        $salesReturn->reject($user);

        return ['message' => trans('messages.success.reject', ['target' => 'Sales Return'])];
    }
}
