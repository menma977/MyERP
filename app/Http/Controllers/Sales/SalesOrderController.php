<?php

namespace App\Http\Controllers\Sales;

use App\Abstracts\ControllerClientDomainAbstract;
use App\Enums\DomainEnum;
use App\Http\Resources\Sales\SalesOrderResource;
use App\Models\Companies\Company;
use App\Models\Sales\SalesOrder;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalesOrderController extends ControllerClientDomainAbstract
{
    /**
     * @return LengthAwarePaginator<int, SalesOrder>|Collection<int, SalesOrder>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $salesOrders = SalesOrder::with([
            'components',
        ])->when($request->input('search'), function ($query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return SalesOrderResource::collection($salesOrders->get());
        }

        if ($request->input('type') === 'count') {
            return $salesOrders->count();
        }

        return SalesOrderResource::collection($salesOrders->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function store(Request $request): array
    {
        if ($this->clientDomain === DomainEnum::CASHER) {
            $request->validate([
                'total' => ['required', 'numeric', 'min:0'],
            ]);
        } else {
            $request->validate([
                'customer_id' => ['required', 'exists:customers,id'],
                'total' => ['required', 'numeric', 'min:0'],
            ]);
        }

        $salesOrder = new SalesOrder;
        $salesOrder->code = CodeGeneratorService::code('SO')->number(SalesOrder::count() + 1)->generate();
        $this->save($salesOrder, $request);

        return [
            'message' => trans('messages.success.store', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    public function show(Request $request): JsonResource
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::withContributors()
            ->withUsers()
            ->with('components')
            ->where('id', $request->route('id'))
            ->firstOrFail();

        return $salesOrder->toResource();
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function update(Request $request): array
    {
        if ($this->clientDomain === DomainEnum::CASHER) {
            $request->validate([
                'total' => ['required', 'numeric', 'min:0'],
            ]);
        } else {
            $request->validate([
                'customer_id' => ['required', 'exists:customers,id'],
                'total' => ['required', 'numeric', 'min:0'],
            ]);
        }

        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::where('id', $request->route('id'))->firstOrFail();
        $this->save($salesOrder, $request);

        return [
            'message' => trans('messages.success.update', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::where('id', $request->route('id'))->firstOrFail();
        $salesOrder->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesOrder->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesOrder->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Sales Order', 'target' => 'Access']),
            ]);
        }

        $salesOrder->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_order: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = SalesOrder::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Sales Order', 'target' => 'Access']),
            ]);
        }

        $salesOrder->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Sales Order']),
            'sales_order' => $salesOrder->toResource(),
        ];
    }

    protected function save(SalesOrder $salesOrder, Request $request): void
    {
        if ($this->clientDomain === DomainEnum::CASHER) {
            $user = Auth::user();
            if (! $user) {
                throw ValidationException::withMessages([
                    'user' => trans('messages.fail.action.cost', ['action' => 'create', 'attribute' => 'Sales Order', 'target' => 'Access']),
                ]);
            }
            $company = $user->currentAccessToken()?->company_id;
            $company = Company::with('customerDefault')->where('id', $company)->first();
            if (! $company) {
                throw ValidationException::withMessages([
                    'company' => trans('messages.fail.action.cost', ['action' => 'create', 'attribute' => 'Sales Order', 'target' => 'Company']),
                ]);
            }

            if (! $company->customerDefault) {
                $customer = $company->makeCustomerDefault();
            } else {
                $customer = $company->customerDefault;
            }

            $salesOrder->customer_id = $customer->id;
        } else {
            $salesOrder->customer_id = $request->input('customer_id');
        }
        $salesOrder->total = $request->input('total');
        $salesOrder->save();
    }
}
