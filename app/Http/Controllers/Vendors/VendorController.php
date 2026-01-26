<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendors\Vendor;
use App\Rules\ValidationWithoutTrashed;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class VendorController extends Controller
{
    /**
     * Vendor Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, Vendor>|Collection<int, Vendor>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $vendors = Vendor::withCount([
            'components',
            'vendorInvoices',
            'vendorAccountPayables',
            'vendorPayments',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where('name', 'like', '%'.$request->input('search').'%')
                ->orWhere('code', 'like', '%'.$request->input('search').'%')
                ->orWhere('email', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $vendors->get();
        }

        return $vendors->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Vendor Show
     *
     * Show specified resource.
     */
    public function show(Request $request): Vendor
    {
        return Vendor::with([
            'components',
        ])->withUsers()->where(
            'id',
            FakeIdTranslationService::model(new Vendor)->key($request->route('id'))->translateUlid()
        )->firstOrFail();
    }

    /**
     * Vendor Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Vendor::class)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
        ]);

        $vendor = new Vendor;
        $vendor->code = $request->input('code');
        $vendor->name = $request->input('name');
        $vendor->address = $request->input('address');
        $vendor->phone = $request->input('phone');
        $vendor->email = $request->input('email');
        $vendor->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Vendor'], App::getLocale()),
        ];
    }

    /**
     * Vendor Update
     *
     * Update specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                new ValidationWithoutTrashed(Vendor::class, 'code', FakeIdTranslationService::model(new Vendor)->key($request->route('id'))->translateUlid()),
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
        ]);

        /** @var Vendor $vendor */
        $vendor = Vendor::findOrFail(FakeIdTranslationService::model(new Vendor)->key($request->route('id'))->translateUlid());
        $vendor->code = $request->input('code');
        $vendor->name = $request->input('name');
        $vendor->address = $request->input('address');
        $vendor->phone = $request->input('phone');
        $vendor->email = $request->input('email');
        $vendor->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Vendor'], App::getLocale()),
        ];
    }

    /**
     * Vendor Delete
     *
     * Remove specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var Vendor $vendor */
        $vendor = Vendor::findOrFail(FakeIdTranslationService::model(new Vendor)->key($request->route('id'))->translateUlid());

        if ($vendor->vendorInvoices()->exists() || $vendor->vendorAccountPayables()->exists() || $vendor->vendorPayments()->exists()) {
            throw ValidationException::withMessages([
                'vendor' => trans('messages.fail.delete.cost', ['attribute' => 'Vendor', 'target' => 'Transactions'], App::getLocale()),
            ]);
        }

        $vendor->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Vendor'], App::getLocale()),
        ];
    }
}
