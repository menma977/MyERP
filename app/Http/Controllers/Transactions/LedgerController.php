<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Transactions\Ledger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LedgerController extends Controller
{
    /**
     * Ledger Index
     *
     * Display a listing of resources.
     *
     * @return LengthAwarePaginator<int, Ledger>|Collection<int, Ledger>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $ledgers = Ledger::with([
            'component',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where('code', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $ledgers->get();
        }

        return $ledgers->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Ledger Show
     *
     * Show specified resource.
     */
    public function show(Request $request): Ledger
    {
        return Ledger::with([
            'component',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail();
    }
}
