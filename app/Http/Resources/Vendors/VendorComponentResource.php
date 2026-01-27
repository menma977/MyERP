<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorComponent
 */
class VendorComponentResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'item_id' => $this->item_id,
            'price' => $this->price,
            'vendor' => VendorResource::make($this->whenLoaded('vendor')),
            'item' => ItemResource::make($this->whenLoaded('item')),
            'event' => ApprovalEventResource::make($this->whenLoaded('event')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
