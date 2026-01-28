<?php

namespace App\Http\Resources\Sales;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Items\GoodIssueResource;
use App\Http\Resources\UserResource;
use App\Models\Sales\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SalesInvoice
 */
class SalesInvoiceResource extends JsonResource
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
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'code' => $this->code,
            'total' => $this->total,
            'tax' => $this->tax,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'fee' => $this->fee,
            'grand_total' => $this->grand_total,
            'note' => $this->note,
            'method' => $this->method,
            'status' => $this->status,
            'order' => SalesOrderResource::make($this->whenLoaded('order')),
            'components' => SalesInvoiceComponentResource::collection($this->whenLoaded('components')),
            'good_issues' => GoodIssueResource::collection($this->whenLoaded('goodIssues')),
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
