<?php

namespace App\Http\Controllers\Sales;

use App\Abstracts\ControllerClientDomainAbstract;
use App\Enums\DomainEnum;
use App\Enums\PaymentMethodEnum;
use App\Http\Resources\Sales\SalesPaymentResource;
use App\Models\Companies\Company;
use App\Models\Sales\SalesPayment;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesPaymentController extends ControllerClientDomainAbstract
{
    /**
     * @return LengthAwarePaginator<int, SalesPayment>|Collection<int, SalesPayment>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $salesPayments = SalesPayment::with([
            'salesInvoice',
            'customer',
        ])->when($request->input('search'), function ($query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%')
                ->orWhere('note', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return SalesPaymentResource::collection($salesPayments->get());
        }

        if ($request->input('type') === 'count') {
            return $salesPayments->count();
        }

        return SalesPaymentResource::collection($salesPayments->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function store(Request $request): array
    {
        $this->validate($request);

        $salesPayment = new SalesPayment;
        $salesPayment->code = CodeGeneratorService::code('SP')->number(SalesPayment::count() + 1)->generate();
        $this->save($salesPayment, $request);

        return [
            'message' => trans('messages.success.store', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    public function show(Request $request): JsonResource
    {
        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::withContributors()
            ->withUsers()
            ->with(['salesInvoice', 'customer'])
            ->where('id', $request->route('id'))
            ->firstOrFail();

        return $salesPayment->toResource();
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function update(Request $request): array
    {
        $this->validate($request);

        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::where('id', $request->route('id'))->firstOrFail();
        $this->save($salesPayment, $request);

        return [
            'message' => trans('messages.success.update', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::where('id', $request->route('id'))->firstOrFail();
        $salesPayment->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesPayment->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $salesPayment->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Sales Payment', 'target' => 'Access']),
            ]);
        }

        $salesPayment->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    /**
     * @return array{message: string, sales_payment: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var SalesPayment $salesPayment */
        $salesPayment = SalesPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Sales Payment', 'target' => 'Access']),
            ]);
        }

        $salesPayment->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Sales Payment']),
            'sales_payment' => $salesPayment->toResource(),
        ];
    }

    protected function save(SalesPayment $salesPayment, Request $request): void
    {
        if ($this->clientDomain === DomainEnum::CASHER) {
            $user = Auth::user();
            if (! $user) {
                throw ValidationException::withMessages([
                    'user' => trans('messages.fail.action.cost', ['action' => 'create', 'attribute' => 'Sales Payment', 'target' => 'Access']),
                ]);
            }
            $company = $user->currentAccessToken()?->company_id;
            $company = Company::with('customerDefault')->where('id', $company)->first();
            if (! $company) {
                throw ValidationException::withMessages([
                    'company' => trans('messages.fail.action.cost', ['action' => 'create', 'attribute' => 'Sales Payment', 'target' => 'Company']),
                ]);
            }

            if (! $company->customerDefault) {
                $customer = $company->makeCustomerDefault();
            } else {
                $customer = $company->customerDefault;
            }

            $salesPayment->customer_id = $customer->id;
        } else {
            $salesPayment->customer_id = $request->input('customer_id');
        }

        $salesPayment->sales_invoice_id = $request->input('sales_invoice_id');
        $salesPayment->total = $request->input('total');
        $salesPayment->method = $request->input('method');
        $salesPayment->paid_at = $request->input('paid_at');
        $salesPayment->note = $request->input('note');
        $salesPayment->save();
    }

    protected function validate(Request $request): void
    {
        if ($this->clientDomain === DomainEnum::CASHER) {
            $request->validate([
                'sales_invoice_id' => ['required', 'exists:sales_invoices,id'],
                'total' => ['required', 'numeric', 'min:0'],
                'method' => ['required', Rule::enum(PaymentMethodEnum::class)],
                'paid_at' => ['nullable', 'date'],
                'note' => ['nullable', 'string'],
            ]);
        } else {
            $request->validate([
                'sales_invoice_id' => ['required', 'exists:sales_invoices,id'],
                'customer_id' => ['required', 'exists:customers,id'],
                'total' => ['required', 'numeric', 'min:0'],
                'method' => ['required', Rule::enum(PaymentMethodEnum::class)],
                'paid_at' => ['nullable', 'date'],
                'note' => ['nullable', 'string'],
            ]);
        }
    }
}
