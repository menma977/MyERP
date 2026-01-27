<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Resources\Items\ItemBatchResource;
use App\Models\Items\ItemBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BatchController extends Controller
{
    /**
     * List item batches with optional filtering and pagination.
     *
     * @param  Request  $request  The HTTP request containing search, sort, and pagination parameters
     * @return LengthAwarePaginator<int, ItemBatch>|Collection<int, ItemBatch>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $batches = ItemBatch::with([
            'item',
            'stock',
        ])->when($request->input('search'), function (Builder $build) use ($request): Builder {
            return $build->where(function (Builder $query) use ($request): Builder {
                return $query
                    ->where('code', 'like', '%'.$request->input('search').'%')
                    ->orWhereHas('item', function (Builder $query) use ($request): Builder {
                        return $query->where('name', 'like', '%'.$request->input('search').'%');
                    });
            });
        })->when($request->input('item_id'), function (Builder $build) use ($request): Builder {
            return $build->where('item_id', $request->input('item_id'));
        })->when($request->input('is_available') !== null, function (Builder $build) use ($request): Builder {
            return $build->where('is_available', $request->input('is_available'));
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ItemBatchResource::collection($batches->get());
        }

        if ($request->input('type') === 'count') {
            return $batches->count();
        }

        return ItemBatchResource::collection($batches->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Show a specific item batch with its related information.
     *
     * @param  Request  $request  The HTTP request
     */
    public function show(Request $request): JsonResource
    {
        return ItemBatch::with([
            'item',
            'stock',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }
}
