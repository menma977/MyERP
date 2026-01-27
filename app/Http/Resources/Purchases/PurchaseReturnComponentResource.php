<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Items\GoodReceiptComponentResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\UserResource;
use App\Models\Purchases\PurchaseReturnComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PurchaseReturnComponent
 */
class PurchaseReturnComponentResource extends JsonResource
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
            'purchase_return_id' => $this->purchase_return_id,
            'purchase_order_component_id' => $this->purchase_order_component_id,
            'good_receipt_component_id' => $this->good_receipt_component_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'note' => $this->note,
            'return' => PurchaseReturnResource::make($this->whenLoaded('return')),
            'order_component' => PurchaseOrderComponentResource::make($this->whenLoaded('orderComponent')),
            'good_receipt_component' => GoodReceiptComponentResource::make($this->whenLoaded('goodReceiptComponent')),
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
