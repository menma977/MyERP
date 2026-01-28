<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vendors\VendorAccountPayableResource;
use App\Models\Vendors\VendorAccountPayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VendorAccountPayableController extends Controller
{
    /**
     * Vendor Account Payable Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorAccountPayable>|Collection<int, VendorAccountPayable>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $vendorAccountPayables = VendorAccountPayable::with([
            'vendor',
            'vendorInvoice',
            'components',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->whereHas('vendor', function (Builder $query) use ($request) {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            })->orWhereHas('vendorInvoice', function (Builder $query) use ($request) {
                $query->where('code', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return VendorAccountPayableResource::collection($vendorAccountPayables->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $vendorAccountPayables->count();
        }

        return VendorAccountPayableResource::collection($vendorAccountPayables->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Vendor Account Payable Show
     *
     * Show specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return VendorAccountPayable::with([
            'vendor',
            'vendorInvoice',
            'components',
        ])->withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Vendor Account Payable Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'vendor_invoice_id' => ['required', 'string', 'exists:vendor_invoices,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $vendorAccountPayable = new VendorAccountPayable;
        $vendorAccountPayable->vendor_id = $request->input('vendor_id');
        $vendorAccountPayable->vendor_invoice_id = $request->input('vendor_invoice_id');
        $vendorAccountPayable->total = $request->input('total');
        $vendorAccountPayable->note = $request->input('note');
        $vendorAccountPayable->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'vendor_invoice_id' => ['required', 'string', 'exists:vendor_invoices,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();
        $vendorAccountPayable->vendor_id = $request->input('vendor_id');
        $vendorAccountPayable->vendor_invoice_id = $request->input('vendor_invoice_id');
        $vendorAccountPayable->total = $request->input('total');
        $vendorAccountPayable->note = $request->input('note');
        $vendorAccountPayable->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();
        $vendorAccountPayable->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $vendorAccountPayable->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $vendorAccountPayable->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Approve
     *
     * Approve specified resource.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Vendor Account Payable', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorAccountPayable->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Reject
     *
     * Reject specified resource.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Vendor Account Payable', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorAccountPayable->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Cancel
     *
     * Cancel specified resource.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Vendor Account Payable', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorAccountPayable->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Rollback
     *
     * Roll back specified resource.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Vendor Account Payable', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorAccountPayable->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }

    /**
     * Vendor Account Payable Force
     *
     * Force to execute action on a specified resource.
     *
     * @return array{message: string, vendor_account_payable: JsonResource}
     */
    public function force(Request $request): array
    {
        $request->validate([
            'step' => ['required', 'string'],
        ]);

        /** @var VendorAccountPayable $vendorAccountPayable */
        $vendorAccountPayable = VendorAccountPayable::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Vendor Account Payable', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorAccountPayable->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Vendor Account Payable'], App::getLocale()),
            'vendor_account_payable' => $vendorAccountPayable->toResource(),
        ];
    }
}
