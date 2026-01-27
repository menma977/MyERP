<?php

namespace Database\Factories\Sales;

use App\Models\Items\Item;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesOrderComponentFactory extends Factory
{
    protected $model = SalesOrderComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'sales_order_id' => SalesOrder::factory(),
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}
