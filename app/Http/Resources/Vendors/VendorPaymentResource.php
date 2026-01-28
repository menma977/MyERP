<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\UserResource;
use App\Models\Vendors\VendorPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VendorPayment
 */
class VendorPaymentResource extends JsonResource
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
            'vendor_account_payable_id' => $this->vendor_account_payable_id,
            'total' => $this->total,
            'method' => $this->method,
            'note' => $this->note,
            'paid_at' => $this->paid_at,
            'vendor' => VendorResource::make($this->whenLoaded('vendor')),
            'account_payable' => VendorAccountPayableResource::make($this->whenLoaded('accountPayable')),
            'components' => VendorPaymentComponentResource::collection($this->whenLoaded('components')),
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
