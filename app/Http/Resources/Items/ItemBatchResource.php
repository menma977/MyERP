<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\UserResource;
use App\Models\Items\ItemBatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemBatch
 */
class ItemBatchResource extends JsonResource
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
            'item_id' => $this->item_id,
            'code' => $this->code,
            'expired_at' => $this->expired_at,
            'is_available' => $this->is_available,
            'item' => ItemResource::make($this->whenLoaded('item')),
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
