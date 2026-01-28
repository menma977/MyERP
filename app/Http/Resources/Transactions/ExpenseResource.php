<?php

namespace App\Http\Resources\Transactions;

use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Models\Transactions\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Expense
 */
class ExpenseResource extends JsonResource
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
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'code' => $this->code,
            'category' => $this->category,
            'method' => $this->method,
            'total' => $this->total,
            'note' => $this->note,
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
