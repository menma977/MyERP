<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\UserResource;
use App\Models\Items\ItemBillComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemBillComponent
 */
class ItemBillComponentResource extends JsonResource
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
            'item_bill_id' => $this->item_bill_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'bill' => ItemBillResource::make($this->whenLoaded('bill')),
            'item' => ItemResource::make($this->whenLoaded('item')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
