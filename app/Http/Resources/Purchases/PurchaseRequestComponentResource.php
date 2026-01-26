<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\Vendors\VendorResource;
use App\Models\Purchases\PurchaseRequestComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PurchaseRequestComponent
 */
class PurchaseRequestComponentResource extends JsonResource
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
            'purchase_request_id' => $this->purchase_request_id,
            'vendor_id' => $this->vendor_id,
            'item_id' => $this->item_id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'note' => $this->note,
            'request' => PurchaseRequestResource::make($this->whenLoaded('request')),
            'vendor' => VendorResource::make($this->whenLoaded('vendor')),
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
