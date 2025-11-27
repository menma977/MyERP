<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\ItemStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StockController extends Controller
{
    /**
     * List item stocks with optional filtering and pagination.
     *
     * @param  Request  $request  The HTTP request containing search, sort, and pagination parameters
     * @return Collection<int, ItemStock>|LengthAwarePaginator<int, ItemStock>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $stocks = ItemStock::with(['batch', 'batch.item'])
            ->when($request->input('search'), function (Builder $build) use ($request): Builder {
                return $build->where(function (Builder $query) use ($request): Builder {
                    return $query
                        ->whereHas('batch', function (Builder $query) use ($request): Builder {
                            return $query->where('code', 'like', '%'.$request->input('search').'%');
                        })
                        ->orWhereHas('batch.item', function (Builder $query) use ($request): Builder {
                            return $query->where('name', 'like', '%'.$request->input('search').'%');
                        });
                });
            })
            ->when($request->input('item_id'), function (Builder $build) use ($request): Builder {
                return $build->whereHas('batch', function (Builder $query) use ($request): Builder {
                    return $query->where('item_id', $request->input('item_id'));
                });
            })
            ->when($request->input('batch_id'), function (Builder $build) use ($request): Builder {
                return $build->where('item_batch_id', $request->input('batch_id'));
            })
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $stocks->get();
        }

        return $stocks->paginate($request->input('per_page', 10));
    }

    /**
     * Show a specific item stock with its related information.
     *
     * @param  Request  $request  The HTTP request
     * @return ItemStock The item stock with loaded relationships
     */
    public function show(Request $request): ItemStock
    {
        return ItemStock::with([
            'batch',
            'batch.item',
        ])->where('id', $request->route('id'))->firstOrFail();
    }
}
