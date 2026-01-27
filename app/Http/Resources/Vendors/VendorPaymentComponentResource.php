<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorPaymentComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorPaymentComponent
 */
class VendorPaymentComponentResource extends JsonResource
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
            'vendor_payment_id' => $this->vendor_payment_id,
            'vendor_account_payable_component_id' => $this->vendor_account_payable_component_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'payment' => VendorPaymentResource::make($this->whenLoaded('payment')),
            'account_payable_component' => VendorAccountPayableComponentResource::make($this->whenLoaded('accountPayableComponent')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
