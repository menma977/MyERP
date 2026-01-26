<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\Purchases\PurchaseRequestResource;
use App\Models\Purchases\PurchaseRequest;
use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseRequestController extends Controller
{
    /**
     * Purchase Request Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, PurchaseRequest>|Collection<int, PurchaseRequest>|JsonResource|int
     *
     * @noinspection DuplicatedCode
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $purchaseRequests = PurchaseRequest::query()
            ->withContributors()
            ->withUsers()
            ->with([
                'components',
            ])->when($request->input('search'), function (Builder $query) use ($request) {
                return $query->where(function (Builder $query) use ($request) {
                    return $query->where('code', 'like', '%'.$request->input('search').'%');
                });
            })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return PurchaseRequestResource::collection($purchaseRequests->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $purchaseRequests->count();
        }

        return PurchaseRequestResource::collection($purchaseRequests->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Purchase Request Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function store(): array
    {
        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'store', 'attribute' => 'Purchase Request', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequest = new PurchaseRequest;
        $purchaseRequest->code = CodeGeneratorService::code('PR')->number(PurchaseRequest::count())->generate();
        $purchaseRequest->total = 0;
        $purchaseRequest->save();

        $purchaseRequest->initEvent($user);

        return [
            'message' => trans('messages.success.store', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return PurchaseRequest::query()
            ->withContributors()
            ->withUsers()
            ->with([
                'components',
            ])->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Purchase Request Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::where('id', $request->route('id'))->firstOrFail();

        if ($purchaseRequest->procurement) {
            throw ValidationException::withMessages([
                'procurement' => trans('messages.fail.action.cost', ['action' => 'delete', 'attribute' => 'Purchase Request', 'target' => 'Procurement'], App::getLocale()),
            ]);
        }

        if ($purchaseRequest->order) {
            throw ValidationException::withMessages([
                'order' => trans('messages.fail.action.cost', ['action' => 'delete', 'attribute' => 'Purchase Request', 'target' => 'Order'], App::getLocale()),
            ]);
        }

        $purchaseRequest->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseRequest->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $purchaseRequest->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Approves
     *
     * Approve the specified purchase request.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Purchase Request', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequest->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Reject
     *
     * Reject the specified purchase request.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Purchase Request', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequest->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Cancel
     *
     * Cancel the specified purchase request.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Purchase Request', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequest->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Rollback
     *
     * Roll back the specified purchase request.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Purchase Request', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequest->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }

    /**
     * Purchase Request Force
     *
     * Force execute action on the specified purchase request.
     *
     * @return array{message: string, purchase_request: JsonResource}
     */
    public function force(Request $request): array
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Purchase Request', 'target' => 'Access'], App::getLocale()),
            ]);
        }

        $purchaseRequest->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Purchase Request'], App::getLocale()),
            'purchase_request' => $purchaseRequest->toResource(),
        ];
    }
}
