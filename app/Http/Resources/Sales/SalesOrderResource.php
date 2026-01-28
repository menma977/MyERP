<?php

namespace App\Http\Resources\Sales;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Sales\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SalesOrder
 */
class SalesOrderResource extends JsonResource
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
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'code' => $this->code,
            'total' => $this->total,
            'components' => SalesOrderComponentResource::collection($this->whenLoaded('components')),
            'invoice' => SalesInvoiceResource::make($this->whenLoaded('invoice')),
            'sales_returns' => SalesReturnResource::collection($this->whenLoaded('salesReturns')),
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
