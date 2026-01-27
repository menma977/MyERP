<?php

namespace App\Http\Resources\Approval;

use App\Http\Resources\UserResource;
use App\Models\Approval\ApprovalFlowComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ApprovalFlowComponent
 */
class ApprovalFlowComponentResource extends JsonResource
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
            'approval_flow_id' => $this->approval_flow_id,
            'approval_dictionary_id' => $this->approval_dictionary_id,
            'key' => $this->key,
            'flow' => ApprovalFlowResource::make($this->whenLoaded('flow')),
            'dictionary' => ApprovalDictionaryResource::make($this->whenLoaded('dictionary')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
