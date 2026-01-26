<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Purchases\PurchaseOrderResource;
use App\Http\Resources\Purchases\PurchaseReturnResource;
use App\Http\Resources\UserResource;
use App\Models\Items\GoodReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GoodReceipt
 */
class GoodReceiptResource extends JsonResource
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
            'code' => $this->code,
            'total' => $this->total,
            'note' => $this->note,
            'order' => PurchaseOrderResource::make($this->whenLoaded('order')),
            'components' => GoodReceiptComponentResource::collection($this->whenLoaded('components')),
            'purchase_returns' => PurchaseReturnResource::collection($this->whenLoaded('purchaseReturns')),
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
