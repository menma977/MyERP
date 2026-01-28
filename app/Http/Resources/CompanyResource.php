<?php

namespace App\Http\Resources;

use App\Http\Resources\Customer\CustomerResource;
use App\Models\Companies\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class CompanyResource extends JsonResource
{
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
            'code' => $this->code,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'default_customer' => CustomerResource::make($this->whenLoaded('customerDefault')),
            'customers' => CustomerResource::collection($this->whenLoaded('customers')),
            'logo' => $this->whenLoaded('logo'),
            'created_by' => $this->whenLoaded('createdBy'),
            'updated_by' => $this->whenLoaded('updatedBy'),
            'deleted_by' => $this->whenLoaded('deletedBy'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
