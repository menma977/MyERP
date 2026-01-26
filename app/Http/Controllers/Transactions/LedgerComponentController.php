<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transactions\LedgerComponentResource;
use App\Models\Transactions\LedgerComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LedgerComponentController extends Controller
{
    /**
     * Ledger Component Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, LedgerComponent>|Collection<int, LedgerComponent>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $ledgerComponents = LedgerComponent::with([
            'ledger',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->whereHas('ledger', function (Builder $query) use ($request) {
                $query->where('code', 'like', '%'.$request->input('search').'%');
            });
        })->where('ledger_id', $request->route('ledger_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return LedgerComponentResource::collection($ledgerComponents->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $ledgerComponents->count();
        }

        return LedgerComponentResource::collection($ledgerComponents->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Ledger Component Show
     *
     * Show specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return LedgerComponent::with([
            'ledger',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }
}
