<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\Purchases\PurchaseInvoiceComponentResource;
use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorAccountPayableComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorAccountPayableComponent
 */
class VendorAccountPayableComponentResource extends JsonResource
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
            'vendor_account_payable_id' => $this->vendor_account_payable_id,
            'purchase_invoice_component_id' => $this->purchase_invoice_component_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'account_payable' => VendorAccountPayableResource::make($this->whenLoaded('accountPayable')),
            'purchase_invoice_component' => PurchaseInvoiceComponentResource::make($this->whenLoaded('purchaseInvoiceComponent')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
