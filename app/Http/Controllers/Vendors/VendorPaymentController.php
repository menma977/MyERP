<?php

namespace App\Http\Controllers\Vendors;

use App\Enums\PaymentMethodEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Vendors\VendorPaymentResource;
use App\Models\Vendors\VendorPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VendorPaymentController extends Controller
{
    /**
     * Vendor Payment Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorPayment>|Collection<int, VendorPayment>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $vendorPayments = VendorPayment::with([
            'vendor',
            'accountPayable',
            'components',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->whereHas('vendor', function (Builder $query) use ($request) {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return VendorPaymentResource::collection($vendorPayments->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $vendorPayments->count();
        }

        return VendorPaymentResource::collection($vendorPayments->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Vendor Payment Show
     *
     * Show specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return VendorPayment::with([
            'vendor',
            'accountPayable',
            'components',
        ])->withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Vendor Payment Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'vendor_account_payable_id' => ['required', 'string', 'exists:vendor_account_payables,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'string', 'in:'.collect(PaymentMethodEnum::cases())->pluck('value')->implode(',')],
            'note' => ['nullable', 'string'],
            'paid_at' => ['nullable', 'date'],
        ]);

        $vendorPayment = new VendorPayment;
        $this->save($request, $vendorPayment);

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'vendor_account_payable_id' => ['required', 'string', 'exists:vendor_account_payables,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'string', 'in:'.collect(PaymentMethodEnum::cases())->pluck('value')->implode(',')],
            'note' => ['nullable', 'string'],
            'paid_at' => ['nullable', 'date'],
        ]);

        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();
        $this->save($request, $vendorPayment);

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();
        $vendorPayment->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $vendorPayment->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $vendorPayment->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Approve
     *
     * Approve specified resource.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Vendor Payment', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorPayment->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Reject
     *
     * Reject specified resource.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Vendor Payment', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorPayment->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Cancel
     *
     * Cancel specified resource.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Vendor Payment', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorPayment->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Rollback
     *
     * Roll back specified resource.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Vendor Payment', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorPayment->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    /**
     * Vendor Payment Force
     *
     * Force to execute action on a specified resource.
     *
     * @return array{message: string, vendor_payment: JsonResource}
     */
    public function force(Request $request): array
    {
        $request->validate([
            'step' => ['required', 'string'],
        ]);

        /** @var VendorPayment $vendorPayment */
        $vendorPayment = VendorPayment::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Vendor Payment', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorPayment->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Vendor Payment'], App::getLocale()),
            'vendor_payment' => $vendorPayment->toResource(),
        ];
    }

    protected function save(Request $request, VendorPayment $vendorPayment): void
    {
        $vendorPayment->vendor_id = $request->input('vendor_id');
        $vendorPayment->vendor_account_payable_id = $request->input('vendor_account_payable_id');
        $vendorPayment->amount = $request->input('amount');
        $vendorPayment->method = PaymentMethodEnum::from($request->input('method'));
        $vendorPayment->note = $request->input('note');
        $vendorPayment->paid_at = $request->input('paid_at');
        $vendorPayment->save();
    }
}
