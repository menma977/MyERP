<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\UserResource;
use App\Models\Purchases\PurchaseProcurement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PurchaseProcurement
 */
class PurchaseProcurementResource extends JsonResource
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
            'code' => $this->code,
            'note' => $this->note,
            'request' => PurchaseRequestResource::make($this->whenLoaded('request')),
            'components' => PurchaseProcurementComponentResource::collection($this->whenLoaded('components')),
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
