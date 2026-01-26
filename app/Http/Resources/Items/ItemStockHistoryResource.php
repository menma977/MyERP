<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\UserResource;
use App\Models\Items\ItemStockHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemStockHistory
 */
class ItemStockHistoryResource extends JsonResource
{
    public bool $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_stock_id' => $this->item_stock_id,
            'code' => $this->code,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'stock' => ItemStockResource::make($this->whenLoaded('stock')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
