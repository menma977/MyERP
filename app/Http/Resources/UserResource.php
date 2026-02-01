<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
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
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'avatar' => FileBucketResource::make($this->whenLoaded('avatar')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'tokens' => PersonalAccessTokenResource::collection($this->whenLoaded('tokens')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
