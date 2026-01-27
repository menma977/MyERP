<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Resources\Items\ItemBillResource;
use App\Models\Items\ItemBill;
use App\Models\Items\ItemBillComponent;
use App\Rules\ValidationWithoutTrashed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ItemBillController extends Controller
{
    /**
     * ItemBill Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, ItemBill>|Collection<int, ItemBill>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $itemBills = ItemBill::with([
            'item',
            'component.item',
        ])->when($request->has('item_id'), function (Builder $query) use ($request) {
            $query->where('item_id', $request->input('item_id'));
        })->when($request->has('code'), function (Builder $query) use ($request) {
            $query->where('code', 'like', '%'.$request->input('code').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return ItemBillResource::collection($itemBills->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $itemBills->count();
        }

        return ItemBillResource::collection($itemBills->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * ItemBill Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ItemBill::with([
            'item',
            'component.item',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * ItemBill Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, item_bill: JsonResource}
     *
     * @throws \Throwable
     */
    public function store(Request $request): array
    {
        $request->validate([
            'item_id' => ['required', 'string', 'exists:items,id'],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(ItemBill::class, 'code')],
            'quantity' => ['required', 'numeric', 'min:0'],
            'components' => ['required', 'array', 'min:1'],
            'components.*.item_id' => ['required', 'string', 'exists:items,id'],
            'components.*.quantity' => ['required', 'numeric', 'min:0'],
        ]);

        $itemBill = DB::transaction(function () use ($request) {
            $itemBill = new ItemBill;
            $itemBill->item_id = $request->input('item_id');
            $itemBill->code = $request->input('code');
            $itemBill->quantity = $request->input('quantity');
            $itemBill->save();

            foreach ($request->input('components') as $componentData) {
                $component = new ItemBillComponent;
                $component->item_bill_id = $itemBill->id;
                $component->item_id = $componentData['item_id'];
                $component->quantity = $componentData['quantity'];
                $component->save();
            }

            return $itemBill;
        });

        return [
            'message' => trans('messages.success.store', ['target' => 'Item Bill'], App::getLocale()),
            'item_bill' => $itemBill->toResource(),
        ];
    }

    /**
     * ItemBill Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, item_bill: JsonResource}
     *
     * @throws \Throwable
     */
    public function update(Request $request): array
    {
        $id = $request->route('id');
        $request->validate([
            'item_id' => ['required', 'string', 'exists:items,id'],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(ItemBill::class, 'code', $id)],
            'quantity' => ['required', 'numeric', 'min:0'],
            'components' => ['required', 'array', 'min:1'],
            'components.*.item_id' => ['required', 'string', 'exists:items,id'],
            'components.*.quantity' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var ItemBill $itemBill */
        $itemBill = ItemBill::findOrFail($id);

        DB::transaction(function () use ($request, $itemBill) {
            $itemBill->item_id = $request->input('item_id');
            $itemBill->code = $request->input('code');
            $itemBill->quantity = $request->input('quantity');
            $itemBill->save();

            $existingComponents = $itemBill->component()->get()->keyBy('item_id');
            $processedItemIds = [];

            foreach ($request->input('components') as $componentData) {
                $itemId = $componentData['item_id'];
                $quantity = $componentData['quantity'];

                if ($existingComponents->has($itemId)) {
                    /** @var ItemBillComponent $component */
                    $component = $existingComponents->get($itemId);
                } else {
                    $component = new ItemBillComponent;
                    $component->item_bill_id = $itemBill->id;
                    $component->item_id = $itemId;
                }

                $component->quantity = $quantity;
                $component->save();

                $processedItemIds[] = $itemId;
            }

            $existingComponents->except($processedItemIds)->each(function (ItemBillComponent $component) {
                $component->delete();
            });
        });

        return [
            'message' => trans('messages.success.update', ['target' => 'Item Bill'], App::getLocale()),
            'item_bill' => $itemBill->toResource(),
        ];
    }

    /**
     * ItemBill Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, item_bill: JsonResource}
     *
     * @throws \Throwable
     */
    public function delete(Request $request): array
    {
        /** @var ItemBill $itemBill */
        $itemBill = ItemBill::where('id', $request->route('id'))->firstOrFail();

        DB::transaction(function () use ($itemBill) {
            $itemBill->component()->delete();
            $itemBill->delete();
        });

        return [
            'message' => trans('messages.success.delete', ['target' => 'Item Bill'], App::getLocale()),
            'item_bill' => $itemBill->toResource(),
        ];
    }

    /**
     * ItemBill Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, item_bill: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var ItemBill $itemBill */
        $itemBill = ItemBill::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $itemBill->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Item Bill'], App::getLocale()),
            'item_bill' => $itemBill->toResource(),
        ];
    }

    /**
     * ItemBill Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, item_bill: JsonResource}
     *
     * @throws \Throwable
     */
    public function destroy(Request $request): array
    {
        /** @var ItemBill $itemBill */
        $itemBill = ItemBill::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();

        DB::transaction(function () use ($itemBill) {
            $itemBill->component()->forceDelete();
            $itemBill->forceDelete();
        });

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Item Bill'], App::getLocale()),
            'item_bill' => $itemBill->toResource(),
        ];
    }
}
