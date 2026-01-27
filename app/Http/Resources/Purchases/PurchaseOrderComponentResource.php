<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\UserResource;
use App\Models\Purchases\PurchaseOrderComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PurchaseOrderComponent
 */
class PurchaseOrderComponentResource extends JsonResource
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
            'purchase_order_id' => $this->purchase_order_id,
            'purchase_request_component_id' => $this->purchase_request_component_id,
            'purchase_procurement_component_id' => $this->purchase_procurement_component_id,
            'item_id' => $this->item_id,
            'request_quantity' => $this->request_quantity,
            'request_price' => $this->request_price,
            'request_total' => $this->request_total,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'note' => $this->note,
            'order' => PurchaseOrderResource::make($this->whenLoaded('order')),
            'request_component' => PurchaseRequestComponentResource::make($this->whenLoaded('requestComponent')),
            'procurement_component' => PurchaseProcurementComponentResource::make($this->whenLoaded('procurementComponent')),
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
