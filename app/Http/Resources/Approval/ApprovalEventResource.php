<?php

namespace App\Http\Resources\Approval;

use App\Http\Resources\UserResource;
use App\Models\Approval\ApprovalEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ApprovalEvent
 */
class ApprovalEventResource extends JsonResource
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
            'step' => $this->step,
            'target' => $this->target,
            'type' => $this->type,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'cancelled_at' => $this->cancelled_at,
            'rollback_at' => $this->rollback_at,
            'approval' => ApprovalResource::make($this->whenLoaded('approval')),
            'requestable' => $this->whenLoaded('requestable'),
            'components' => ApprovalEventComponentResource::collection($this->whenLoaded('components')),
            'contributors' => ApprovalEventContributorResource::collection($this->whenLoaded('contributors')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
