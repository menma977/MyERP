<?php

namespace Database\Factories\Items;

use App\Models\Items\ItemStock;
use App\Models\Items\ItemStockHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\ItemStockHistory>
 */
class ItemStockHistoryFactory extends Factory
{
    protected $model = ItemStockHistory::class;

    public function definition(): array
    {
        return [
            'item_stock_id' => ItemStock::factory(),
            'code' => $this->faker->unique()->bothify('HIST-####-????'),
            'quantity' => $this->faker->randomFloat(2, -100, 100),
            'price' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
