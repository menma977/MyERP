<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\Customer\Customer;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, Customer>|Collection<int, Customer>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $customers = Customer::with([
            'company',
        ])->when($request->input('search'), function ($query) use ($request) {
            return $query->where(function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('code', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return CustomerResource::collection($customers->get());
        }

        if ($request->input('type') === 'count') {
            return $customers->count();
        }

        return CustomerResource::collection($customers->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * @return array{message: string, customer: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_default' => ['boolean'],
        ]);

        if ($request->boolean('is_default')) {
            if (Customer::where('is_default', true)->exists()) {
                throw ValidationException::withMessages([
                    'is_default' => trans('messages.fail.exist.cost', ['action' => 'create', 'attribute' => 'Company', 'target' => 'Default Customer']),
                ]);
            }
        }

        $customer = new Customer;
        $customer->code = CodeGeneratorService::code('CST')->number(Customer::count() + 1)->generate();
        $customer->name = $request->input('name');
        $customer->email = $request->input('email');
        $customer->phone = $request->input('phone');
        $customer->address = $request->input('address');
        $customer->is_default = $request->boolean('is_default');
        $customer->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Customer']),
            'customer' => $customer->toResource(),
        ];
    }

    public function show(Request $request): JsonResource
    {
        $customer = Customer::with([
            'company',
        ])->where('id', $request->route('id'))->withUsers()->firstOrFail();

        return $customer->toResource();
    }

    /**
     * @return array{message: string, customer: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_default' => ['boolean'],
        ]);

        $customer = Customer::where('id', $request->route('id'))->firstOrFail();

        if ($request->boolean('is_default')) {
            if (Customer::where('is_default', true)->where('id', '!=', $customer->id)->exists()) {
                throw ValidationException::withMessages([
                    'is_default' => trans('messages.fail.exist.cost', ['action' => 'update', 'attribute' => 'Company', 'target' => 'Default Customer']),
                ]);
            }
        }

        $customer->name = $request->input('name');
        $customer->email = $request->input('email');
        $customer->phone = $request->input('phone');
        $customer->address = $request->input('address');
        $customer->is_default = $request->boolean('is_default');
        $customer->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Customer']),
            'customer' => $customer->toResource(),
        ];
    }

    /**
     * @return array{message: string, customer: JsonResource}
     */
    public function delete(Request $request): array
    {
        $customer = Customer::where('id', $request->route('id'))->firstOrFail();
        $customer->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Customer']),
            'customer' => $customer->toResource(),
        ];
    }

    /**
     * @return array{message: string, customer: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var Customer $customer */
        $customer = Customer::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $customer->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Customer']),
            'customer' => $customer->toResource(),
        ];
    }

    /**
     * @return array{message: string, customer: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var Customer $customer */
        $customer = Customer::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $customer->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Customer']),
            'customer' => $customer->toResource(),
        ];
    }
}
