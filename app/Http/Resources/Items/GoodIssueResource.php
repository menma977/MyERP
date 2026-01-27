<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\Approval\ApprovalEventResource;
use App\Http\Resources\Sales\SalesInvoiceResource;
use App\Http\Resources\UserResource;
use App\Models\Items\GoodIssue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GoodIssue
 */
class GoodIssueResource extends JsonResource
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
            'sales_invoice_id' => $this->sales_invoice_id,
            'code' => $this->code,
            'cogs' => $this->cogs,
            'total' => $this->total,
            'note' => $this->note,
            'sales_invoice' => SalesInvoiceResource::make($this->whenLoaded('salesInvoice')),
            'components' => GoodIssueComponentResource::collection($this->whenLoaded('components')),
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
