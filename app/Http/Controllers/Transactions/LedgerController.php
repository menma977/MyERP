<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transactions\LedgerResource;
use App\Models\Transactions\Ledger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LedgerController extends Controller
{
    /**
     * Ledger Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, Ledger>|Collection<int, Ledger>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $ledgers = Ledger::with([
            'component',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return LedgerResource::collection($ledgers->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $ledgers->count();
        }

        return LedgerResource::collection($ledgers->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Ledger Show
     *
     * Show specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return Ledger::with([
            'component',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }
}
