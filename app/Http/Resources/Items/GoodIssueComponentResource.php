<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\Sales\SalesInvoiceComponentResource;
use App\Http\Resources\UserResource;
use App\Models\Items\GoodIssueComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GoodIssueComponent
 */
class GoodIssueComponentResource extends JsonResource
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
            'good_issue_id' => $this->good_issue_id,
            'sales_invoice_component_id' => $this->sales_invoice_component_id,
            'item_id' => $this->item_id,
            'item_batch_id' => $this->item_batch_id,
            'item_stock_id' => $this->item_stock_id,
            'quantity' => $this->quantity,
            'cogs' => $this->cogs,
            'good' => GoodIssueResource::make($this->whenLoaded('good')),
            'sales_invoice_component' => SalesInvoiceComponentResource::make($this->whenLoaded('salesInvoiceComponent')),
            'item' => ItemResource::make($this->whenLoaded('item')),
            'batch' => ItemBatchResource::make($this->whenLoaded('batch')),
            'stock' => ItemStockResource::make($this->whenLoaded('stock')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'updated_by' => UserResource::make($this->whenLoaded('updatedBy')),
            'deleted_by' => UserResource::make($this->whenLoaded('deletedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
