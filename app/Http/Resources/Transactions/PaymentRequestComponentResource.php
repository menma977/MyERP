<?php

namespace App\Http\Resources\Transactions;

use App\Http\Resources\Purchases\PurchaseInvoiceComponentResource;
use App\Http\Resources\Purchases\PurchaseOrderComponentResource;
use App\Http\Resources\UserResource;
use App\Models\Transactions\PaymentRequestComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaymentRequestComponent
 */
class PaymentRequestComponentResource extends JsonResource
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
            'payment_request_id' => $this->payment_request_id,
            'purchase_order_component_id' => $this->purchase_order_component_id,
            'purchase_invoice_component_id' => $this->purchase_invoice_component_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'note' => $this->note,
            'payment_request' => PaymentRequestResource::make($this->whenLoaded('paymentRequest')),
            'purchase_order_component' => PurchaseOrderComponentResource::make($this->whenLoaded('purchaseOrderComponent')),
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
