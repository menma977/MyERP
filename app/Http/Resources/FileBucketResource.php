<?php

namespace App\Http\Resources;

use App\Models\FileBucket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FileBucket
 */
class FileBucketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'model' => $this->whenLoaded('model'),
            'name' => $this->name,
            'url' => $this->url,
            'mime_type' => $this->mime_type,
            'mime' => $this->mime,
            'extension' => $this->extension,
            'size' => $this->size,
            'tags' => $this->tags,
            'finished_at' => $this->finished_at,
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
