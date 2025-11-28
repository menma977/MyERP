<?php

namespace App\Http\Controllers\Items;

use App\Enums\ItemTypeEnum;
use App\Enums\ItemUnitEnum;
use App\Http\Controllers\Controller;
use App\Models\Items\Item;
use App\Rules\ValidationWithoutTrashed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    /**
     * List items with optional filtering and pagination.
     *
     * @param  Request  $request  The HTTP request containing search, sort, and pagination parameters
     * @return Collection<int, Item>|LengthAwarePaginator<int, Item>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $items = Item::withCount('batches')->when($request->input('search'), function (Builder $build) use ($request): Builder {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $items->get();
        }

        return $items->paginate($request->input('per_page', 10));
    }

    /**
     * Show a specific item with its batches and stock information.
     *
     * @param  Request  $request  The HTTP request
     * @return Item The item with loaded relationships
     */
    public function show(Request $request): Item
    {
        return Item::with([
            'batches.stock',
        ])->where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Store a new item in storage.
     *
     * @param  Request  $request  The HTTP request containing item data
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Item::class)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:'.collect(ItemTypeEnum::cases())->implode(',')],
            'unit' => ['required', 'string', 'max:255', 'in:'.collect(ItemUnitEnum::cases())->implode(',')],
        ]);

        $item = new Item;
        $item->code = $request->input('code');
        $item->name = $request->input('name');
        $item->type = $request->input('type');
        $item->unit = $request->input('unit');
        $item->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Item'], App::getLocale()),
        ];
    }

    /**
     * Update an existing item in storage.
     *
     * @param  Request  $request  The HTTP request containing updated item data
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Item::class, 'code', $request->route('id'))],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:'.collect(ItemTypeEnum::cases())->implode(',')],
            'unit' => ['required', 'string', 'max:255', 'in:'.collect(ItemUnitEnum::cases())->implode(',')],
        ]);

        /** @var Item $item */
        $item = Item::findOrFail($request->route('id'));
        $item->code = $request->input('code');
        $item->name = $request->input('name');
        $item->type = $request->input('type');
        $item->unit = $request->input('unit');
        $item->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Item'], App::getLocale()),
        ];
    }

    /**
     * Soft delete an item.
     *
     * @param  Request  $request  The HTTP request
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var Item $item */
        $item = Item::findOrFail($request->route('id'));

        if ($item->batches()->exists()) {
            throw ValidationException::withMessages([
                'batches' => trans('messages.fail.delete.cost', ['attribute' => 'Item', 'target' => 'Batch'], App::getLocale()),
            ]);
        }

        $item->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Item'], App::getLocale()),
        ];
    }

    /**
     * Permanently delete an item.
     *
     * @param  Request  $request  The HTTP request
     * @return array{message: string}
     */
    public function destroy(Request $request): array
    {
        /** @var Item $item */
        $item = Item::withTrashed()->findOrFail($request->route('id'));
        if (! $item->trashed()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.action.cost', ['attribute' => 'Item', 'target' => 'Trash Status', 'action' => 'destroy'], App::getLocale()),
            ]);
        }
        $item->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Item'], App::getLocale()),
        ];
    }

    /**
     * Restore a soft deleted item.
     *
     * @param  Request  $request  The HTTP request
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var Item $item */
        $item = Item::onlyTrashed()->findOrFail($request->route('id'));
        $item->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Item'], App::getLocale()),
        ];
    }
}
