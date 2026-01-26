<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorComponentHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorComponentHistory
 */
class VendorComponentHistoryResource extends JsonResource
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
            'vendor_component_id' => $this->vendor_component_id,
            'price' => $this->price,
            'vendor_component' => VendorComponentResource::make($this->whenLoaded('vendorComponent')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
