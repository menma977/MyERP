<?php

namespace App\Http\Controllers\Items;

use App\Enums\ItemTypeEnum;
use App\Enums\ItemUnitEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Items\ItemResource;
use App\Models\Items\Item;
use App\Rules\ValidationWithoutTrashed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    /**
     * List items with optional filtering and pagination.
     *
     * @param  Request  $request  The HTTP request containing search, sort, and pagination parameters
     * @return LengthAwarePaginator<int, Item>|Collection<int, Item>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $items = Item::withCount('batches')->when($request->input('search'), function (Builder $build) use ($request): Builder {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ItemResource::collection($items->get());
        }

        if ($request->input('type') === 'count') {
            return $items->count();
        }

        return ItemResource::collection($items->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Show a specific item with its batches and stock information.
     *
     * @param  Request  $request  The HTTP request
     */
    public function show(Request $request): JsonResource
    {
        return Item::with([
            'batches.stock',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Store a new item in storage.
     *
     * @param  Request  $request  The HTTP request containing item data
     * @return array{message: string, item: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Item::class)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(ItemTypeEnum::cases(), 'value'))],
            'unit' => ['required', 'string', 'max:255', 'in:'.implode(',', array_column(ItemUnitEnum::cases(), 'value'))],
        ]);

        $item = new Item;
        $item->code = $request->input('code');
        $item->name = $request->input('name');
        $item->type = $request->input('type');
        $item->unit = $request->input('unit');
        $item->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Item'], App::getLocale()),
            'item' => $item->toResource(),
        ];
    }

    /**
     * Update an existing item in storage.
     *
     * @param  Request  $request  The HTTP request containing updated item data
     * @return array{message: string, item: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Item::class, 'code', $request->route('id'))],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(ItemTypeEnum::cases(), 'value'))],
            'unit' => ['required', 'string', 'max:255', 'in:'.implode(',', array_column(ItemUnitEnum::cases(), 'value'))],
        ]);

        /** @var Item $item */
        $item = Item::where('id', $request->route('id'))->firstOrFail();
        $item->code = $request->input('code');
        $item->name = $request->input('name');
        $item->type = $request->input('type');
        $item->unit = $request->input('unit');
        $item->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Item'], App::getLocale()),
            'item' => $item->toResource(),
        ];
    }

    /**
     * Soft delete an item.
     *
     * @param  Request  $request  The HTTP request
     * @return array{message: string, item: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var Item $item */
        $item = Item::where('id', $request->route('id'))->firstOrFail();

        if ($item->batches()->exists()) {
            throw ValidationException::withMessages([
                'batches' => trans('messages.fail.delete.cost', ['attribute' => 'Item', 'target' => 'Batch'], App::getLocale()),
            ]);
        }

        $item->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Item'], App::getLocale()),
            'item' => $item->toResource(),
        ];
    }

    /**
     * Permanently delete an item.
     *
     * @param  Request  $request  The HTTP request
     * @return array{message: string, item: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var Item $item */
        $item = Item::withTrashed()->where('id', $request->route('id'))->firstOrFail();
        if (! $item->trashed()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.action.cost', ['attribute' => 'Item', 'target' => 'Trash Status', 'action' => 'destroy'], App::getLocale()),
            ]);
        }
        $item->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Item'], App::getLocale()),
            'item' => $item->toResource(),
        ];
    }

    /**
     * Restore a soft deleted item.
     *
     * @param  Request  $request  The HTTP request
     * @return array{message: string, item: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var Item $item */
        $item = Item::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $item->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Item'], App::getLocale()),
            'item' => $item->toResource(),
        ];
    }
}
