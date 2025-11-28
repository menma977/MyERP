<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\ItemStockHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StockHistoryController extends Controller
{
    /**
     * List item stock histories with optional filtering and pagination.
     *
     * @param  Request  $request  The HTTP request containing search, sort, and pagination parameters
     * @return Collection<int, ItemStockHistory>|LengthAwarePaginator<int, ItemStockHistory>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $histories = ItemStockHistory::with(['stock', 'stock.batch', 'stock.batch.item'])
            ->when($request->input('search'), function (Builder $build) use ($request): Builder {
                return $build->where(function (Builder $query) use ($request): Builder {
                    return $query
                        ->where('code', 'like', '%'.$request->input('search').'%')
                        ->orWhereHas('stock', function (Builder $query) use ($request): Builder {
                            return $query->whereHas('batch', function (Builder $query) use ($request): Builder {
                                return $query->where('code', 'like', '%'.$request->input('search').'%');
                            });
                        })
                        ->orWhereHas('stock.batch.item', function (Builder $query) use ($request): Builder {
                            return $query->where('name', 'like', '%'.$request->input('search').'%');
                        });
                });
            })
            ->when($request->input('item_id'), function (Builder $build) use ($request): Builder {
                return $build->whereHas('stock', function (Builder $query) use ($request): Builder {
                    return $query->whereHas('batch', function (Builder $query) use ($request): Builder {
                        return $query->where('item_id', $request->input('item_id'));
                    });
                });
            })
            ->when($request->input('batch_id'), function (Builder $build) use ($request): Builder {
                return $build->whereHas('stock', function (Builder $query) use ($request): Builder {
                    return $query->where('item_batch_id', $request->input('batch_id'));
                });
            })
            ->when($request->input('stock_id'), function (Builder $build) use ($request): Builder {
                return $build->where('item_stock_id', $request->input('stock_id'));
            })
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $histories->get();
        }

        return $histories->paginate($request->input('per_page', 10));
    }

    /**
     * Show a specific item stock history with its related information.
     *
     * @param  Request  $request  The HTTP request
     * @return ItemStockHistory The item stock history with loaded relationships
     */
    public function show(Request $request): ItemStockHistory
    {
        return ItemStockHistory::with([
            'stock',
            'stock.batch',
            'stock.batch.item',
        ])->where('id', $request->route('id'))->firstOrFail();
    }
}
