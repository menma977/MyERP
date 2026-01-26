<?php

namespace App\Http\Resources\Approval;

use App\Http\Resources\UserResource;
use App\Models\Approval\ApprovalGroupContributor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ApprovalGroupContributor
 */
class ApprovalGroupContributorResource extends JsonResource
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
            'approval_group_id' => $this->approval_group_id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'group' => ApprovalGroupResource::make($this->whenLoaded('group')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
