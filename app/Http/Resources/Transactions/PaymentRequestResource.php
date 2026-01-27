<?php

namespace App\Http\Resources\Transactions;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Purchases\PurchaseInvoiceResource;
use App\Http\Resources\Purchases\PurchaseOrderResource;
use App\Http\Resources\UserResource;
use App\Models\Transactions\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaymentRequest
 */
class PaymentRequestResource extends JsonResource
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
            'purchase_order_id' => $this->purchase_order_id,
            'purchase_invoice_id' => $this->purchase_invoice_id,
            'code' => $this->code,
            'method' => $this->method,
            'total' => $this->total,
            'tax' => $this->tax,
            'note' => $this->note,
            'order' => PurchaseOrderResource::make($this->whenLoaded('order')),
            'invoice' => PurchaseInvoiceResource::make($this->whenLoaded('invoice')),
            'components' => PaymentRequestComponentResource::collection($this->whenLoaded('components')),
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
