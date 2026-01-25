<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendors\VendorInvoice;
use App\Rules\ValidationWithoutTrashed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VendorInvoiceController extends Controller
{
    /**
     * Vendor Invoice Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, VendorInvoice>|Collection<int, VendorInvoice>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $vendorInvoices = VendorInvoice::with([
            'vendor',
            'components',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%')
                ->orWhereHas('vendor', function (Builder $query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('search').'%');
                });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $vendorInvoices->get();
        }

        return $vendorInvoices->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Vendor Invoice Show
     *
     * Show specified resource.
     */
    public function show(Request $request): VendorInvoice
    {
        return VendorInvoice::with([
            'vendor',
            'components',
        ])->withContributors()->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Vendor Invoice Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(VendorInvoice::class)],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        $vendorInvoice = new VendorInvoice;
        $vendorInvoice->vendor_id = $request->input('vendor_id');
        $vendorInvoice->code = $request->input('code');
        $vendorInvoice->total = $request->input('total');
        $vendorInvoice->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(VendorInvoice::class, 'code', $request->route('id'))],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));
        $vendorInvoice->vendor_id = $request->input('vendor_id');
        $vendorInvoice->code = $request->input('code');
        $vendorInvoice->total = $request->input('total');
        $vendorInvoice->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));
        $vendorInvoice->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Restore
     *
     * Restore specified resource from storage.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::onlyTrashed()->findOrFail($request->route('id'));
        $vendorInvoice->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Destroy
     *
     * Permanently remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::onlyTrashed()->findOrFail($request->route('id'));
        $vendorInvoice->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Approve
     *
     * Approve specified resource.
     *
     * @return array{message: string}
     */
    public function approve(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Vendor Invoice', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorInvoice->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Reject
     *
     * Reject specified resource.
     *
     * @return array{message: string}
     */
    public function reject(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Vendor Invoice', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorInvoice->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Cancel
     *
     * Cancel specified resource.
     *
     * @return array{message: string}
     */
    public function cancel(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Vendor Invoice', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorInvoice->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Rollback
     *
     * Roll back specified resource.
     *
     * @return array{message: string}
     */
    public function rollback(Request $request): array
    {
        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Vendor Invoice', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorInvoice->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }

    /**
     * Vendor Invoice Force
     *
     * Force to execute action on a specified resource.
     *
     * @return array{message: string}
     */
    public function force(Request $request): array
    {
        $request->validate([
            'step' => ['required', 'string'],
        ]);

        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::findOrFail($request->route('id'));

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Vendor Invoice', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $vendorInvoice->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Vendor Invoice'], App::getLocale()),
        ];
    }
}
