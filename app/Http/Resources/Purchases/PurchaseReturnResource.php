<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Items\GoodReceiptResource;
use App\Http\Resources\UserResource;
use App\Models\Purchases\PurchaseReturn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PurchaseReturn
 */
class PurchaseReturnResource extends JsonResource
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
            'good_receipt_id' => $this->good_receipt_id,
            'code' => $this->code,
            'total' => $this->total,
            'note' => $this->note,
            'order' => PurchaseOrderResource::make($this->whenLoaded('order')),
            'good_receipt' => GoodReceiptResource::make($this->whenLoaded('goodReceipt')),
            'components' => PurchaseReturnComponentResource::collection($this->whenLoaded('components')),
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
