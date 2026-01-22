<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\SalesOrder;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalesOrderController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, SalesOrder>|Collection<int, SalesOrder>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $salesOrders = SalesOrder::query()->with([
            'components',
        ])->when($request->input('search'), function ($query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $salesOrders->get();
        }

        return $salesOrders->withContributors()->withUsers()->paginate($request->input('per_page', 10));
    }

    /**
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $salesOrder = new SalesOrder;
        $salesOrder->code = CodeGeneratorService::code('SO')->number(SalesOrder::count() + 1)->generate();
        $salesOrder->total = $request->input('total');
        $salesOrder->save();

        return ['message' => trans('messages.success.store', ['target' => 'Sales Order'])];
    }

    public function show(Request $request): SalesOrder
    {
        return SalesOrder::query()
            ->withContributors()
            ->withUsers()
            ->with('components')
            ->where('id', $request->route('id'))
            ->firstOrFail();
    }

    /**
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::findOrFail($request->route('id'));
        $salesOrder->total = $request->input('total');
        $salesOrder->save();

        return ['message' => trans('messages.success.update', ['target' => 'Sales Order'])];
    }

    /**
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::findOrFail($request->route('id'));
        $salesOrder->delete();

        return ['message' => trans('messages.success.delete', ['target' => 'Sales Order'])];
    }

    /**
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::onlyTrashed()->findOrFail($request->route('id'));
        $salesOrder->restore();

        return ['message' => trans('messages.success.restore', ['target' => 'Sales Order'])];
    }

    /**
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::onlyTrashed()->findOrFail($request->route('id'));
        $salesOrder->forceDelete();

        return ['message' => trans('messages.success.destroy', ['target' => 'Sales Order'])];
    }

    /**
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Sales Order', 'target' => 'Access']),
            ]);
        }

        $salesOrder->approve($user);

        return ['message' => trans('messages.success.approve', ['target' => 'Sales Order'])];
    }

    /**
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Sales Order', 'target' => 'Access']),
            ]);
        }

        $salesOrder->reject($user);

        return ['message' => trans('messages.success.reject', ['target' => 'Sales Order'])];
    }
}
