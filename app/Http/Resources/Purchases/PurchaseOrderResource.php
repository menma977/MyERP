<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Items\GoodReceiptResource;
use App\Http\Resources\UserResource;
use App\Models\Purchases\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PurchaseOrder
 */
class PurchaseOrderResource extends JsonResource
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
            'purchase_request_id' => $this->purchase_request_id,
            'purchase_procurement_id' => $this->purchase_procurement_id,
            'code' => $this->code,
            'request_total' => $this->request_total,
            'total' => $this->total,
            'note' => $this->note,
            'request' => PurchaseRequestResource::make($this->whenLoaded('request')),
            'procurement' => PurchaseProcurementResource::make($this->whenLoaded('procurement')),
            'good_receipts' => GoodReceiptResource::collection($this->whenLoaded('goodReceipts')),
            'return' => PurchaseReturnResource::make($this->whenLoaded('return')),
            'components' => PurchaseOrderComponentResource::collection($this->whenLoaded('components')),
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
