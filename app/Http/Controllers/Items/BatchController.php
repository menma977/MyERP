<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\ItemBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BatchController extends Controller
{
    /**
     * List item batches with optional filtering and pagination.
     *
     * @param  Request  $request  The HTTP request containing search, sort, and pagination parameters
     * @return Collection<int, ItemBatch>|LengthAwarePaginator<int, ItemBatch>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $batches = ItemBatch::with(['item', 'stock'])
            ->when($request->input('search'), function (Builder $build) use ($request): Builder {
                return $build->where(function (Builder $query) use ($request): Builder {
                    return $query
                        ->where('code', 'like', '%'.$request->input('search').'%')
                        ->orWhereHas('item', function (Builder $query) use ($request): Builder {
                            return $query->where('name', 'like', '%'.$request->input('search').'%');
                        });
                });
            })
            ->when($request->input('item_id'), function (Builder $build) use ($request): Builder {
                return $build->where('item_id', $request->input('item_id'));
            })
            ->when($request->input('is_available') !== null, function (Builder $build) use ($request): Builder {
                return $build->where('is_available', $request->input('is_available'));
            })
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $batches->get();
        }

        return $batches->paginate($request->input('per_page', 10));
    }

    /**
     * Show a specific item batch with its related information.
     *
     * @param  Request  $request  The HTTP request
     * @return ItemBatch The item batch with loaded relationships
     */
    public function show(Request $request): ItemBatch
    {
        return ItemBatch::with([
            'item',
            'stock',
        ])->where('id', $request->route('id'))->firstOrFail();
    }
}
