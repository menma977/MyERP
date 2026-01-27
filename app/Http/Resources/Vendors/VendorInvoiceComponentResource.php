<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorInvoiceComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorInvoiceComponent
 */
class VendorInvoiceComponentResource extends JsonResource
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
            'vendor_invoice_id' => $this->vendor_invoice_id,
            'vendor_component_id' => $this->vendor_component_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'invoice' => VendorInvoiceResource::make($this->whenLoaded('invoice')),
            'vendor_component' => VendorComponentResource::make($this->whenLoaded('vendorComponent')),
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
