<?php

namespace Database\Factories\Sales;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\Sales\SalesReturn;
use App\Models\Sales\SalesReturnComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesReturnComponent>
 */
class SalesReturnComponentFactory extends Factory
{
    protected $model = SalesReturnComponent::class;

    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 100);
        $price = fake()->randomFloat(2, 10, 1000);

        return [
            'sales_return_id' => SalesReturn::factory(),
            'item_id' => Item::factory(),
            'item_batch_id' => ItemBatch::factory(),
            'item_stock_id' => ItemStock::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
