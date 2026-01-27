<?php

namespace App\Http\Resources\Sales;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\UserResource;
use App\Models\Sales\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SalesReturn
 */
class SalesReturnResource extends JsonResource
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
            'sales_order_id' => $this->sales_order_id,
            'sales_invoice_id' => $this->sales_invoice_id,
            'code' => $this->code,
            'total' => $this->total,
            'order' => SalesOrderResource::make($this->whenLoaded('order')),
            'invoice' => SalesInvoiceResource::make($this->whenLoaded('invoice')),
            'components' => SalesReturnComponentResource::collection($this->whenLoaded('components')),
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
