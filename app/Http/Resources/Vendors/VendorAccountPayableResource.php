<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorAccountPayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorAccountPayable
 */
class VendorAccountPayableResource extends JsonResource
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
            'vendor_invoice_id' => $this->vendor_invoice_id,
            'amount' => $this->amount,
            'note' => $this->note,
            'vendor' => VendorResource::make($this->whenLoaded('vendor')),
            'vendor_invoice' => VendorInvoiceResource::make($this->whenLoaded('vendorInvoice')),
            'components' => VendorAccountPayableComponentResource::collection($this->whenLoaded('components')),
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
