<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\Purchases\PurchaseProcurementResource;
use App\Models\Purchases\PurchaseProcurement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseProcurementController extends Controller
{
    /**
     * Purchase Procurement Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseProcurement>|Collection<int, PurchaseProcurement>|JsonResource|int
     *
     * @noinspection DuplicatedCode
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $purchaseProcurements = PurchaseProcurement::with([
            'request',
            'components',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query->where('code', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return PurchaseProcurementResource::collection($purchaseProcurements->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $purchaseProcurements->count();
        }

        return PurchaseProcurementResource::collection($purchaseProcurements->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Purchase Procurement Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::withContributors()
            ->withUsers()
            ->with([
                'request',
                'components',
            ])->where('id', $request->route('id'))->firstOrFail();

        return $purchaseProcurement->toResource();
    }

    /**
     * Purchase Procurement Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'purchase_request_id' => ['required', 'string', 'exists:purchase_requests,id'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::where('id', $request->route('id'))->firstOrFail();
        $purchaseProcurement->note = $request->input('note');
        $purchaseProcurement->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseProcurement->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseProcurement->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Approves
     *
     * Approve the specified purchase procurement.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Procurement', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseProcurement->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Reject
     *
     * Reject the specified purchase procurement.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Procurement', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseProcurement->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Cancel
     *
     * Cancel the specified purchase procurement.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Procurement', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseProcurement->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Rollback
     *
     * Roll back the specified purchase procurement.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Procurement', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseProcurement->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }

    /**
     * Purchase Procurement Force
     *
     * Force execute action on the specified purchase procurement.
     *
     * @return array{message: string, purchase_procurement: JsonResource}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseProcurement $purchaseProcurement */
        $purchaseProcurement = PurchaseProcurement::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Procurement', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseProcurement->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Procurement'], App::getLocale()),
            'purchase_procurement' => $purchaseProcurement->toResource(),
        ];
    }
}
