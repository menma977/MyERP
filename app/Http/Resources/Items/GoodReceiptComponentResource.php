<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\Purchases\PurchaseOrderComponentResource;
use App\Http\Resources\UserResource;
use App\Models\Items\GoodReceiptComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GoodReceiptComponent
 */
class GoodReceiptComponentResource extends JsonResource
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
            'purchase_order_component_id' => $this->purchase_order_component_id,
            'good_receipt_id' => $this->good_receipt_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'expired_at' => $this->expired_at,
            'purchase_order_component' => PurchaseOrderComponentResource::make($this->whenLoaded('purchaseOrderComponent')),
            'good_receipt' => GoodReceiptResource::make($this->whenLoaded('goodReceipt')),
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
