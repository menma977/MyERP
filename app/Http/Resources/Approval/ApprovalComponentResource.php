<?php

namespace App\Http\Resources\Approval;

use App\Http\Resources\UserResource;
use App\Models\Approval\ApprovalComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ApprovalComponent
 */
class ApprovalComponentResource extends JsonResource
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
            'id' => $this->ulid,
            'name' => $this->name,
            'step' => $this->step,
            'type' => $this->type,
            'color' => $this->color,
            'can_drag' => $this->can_drag,
            'can_edit' => $this->can_edit,
            'can_delete' => $this->can_delete,
            'approval' => ApprovalResource::make($this->whenLoaded('approval')),
            'contributors' => ApprovalContributorResource::collection($this->whenLoaded('contributors')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
