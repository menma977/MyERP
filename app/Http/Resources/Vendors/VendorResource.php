<?php

namespace App\Http\Resources\Vendors;

use App\Http\Resources\Purchases\PurchaseProcurementComponentResource;
use App\Http\Resources\Purchases\PurchaseRequestComponentResource;
use App\Http\Resources\UserResource;
use App\Models\Vendors\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vendor
 */
class VendorResource extends JsonResource
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
            'id' => $this->ulid,
            'code' => $this->code,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'purchase_procurement_components' => PurchaseProcurementComponentResource::collection($this->whenLoaded('purchaseProcurementComponents')),
            'purchase_request_components' => PurchaseRequestComponentResource::collection($this->whenLoaded('purchaseRequestComponents')),
            'vendor_account_payables' => VendorAccountPayableResource::collection($this->whenLoaded('vendorAccountPayables')),
            'components' => VendorComponentResource::collection($this->whenLoaded('components')),
            'vendor_invoices' => VendorInvoiceResource::collection($this->whenLoaded('vendorInvoices')),
            'vendor_payments' => VendorPaymentResource::collection($this->whenLoaded('vendorPayments')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
