<?php

namespace App\Http\Resources\Sales;

use App\Http\Resources\Items\ItemBatchResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\Items\ItemStockResource;
use App\Http\Resources\UserResource;
use App\Models\Sales\SalesReturnComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SalesReturnComponent
 */
class SalesReturnComponentResource extends JsonResource
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
            'sales_return_id' => $this->sales_return_id,
            'item_id' => $this->item_id,
            'item_batch_id' => $this->item_batch_id,
            'item_stock_id' => $this->item_stock_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'return' => SalesReturnResource::make($this->whenLoaded('return')),
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
