<?php

namespace Database\Factories\Items;

use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\ItemStock>
 */
class ItemStockFactory extends Factory
{
    protected $model = ItemStock::class;

    public function definition(): array
    {
        return [
            'item_batch_id' => ItemBatch::factory(),
            'quantity' => $this->faker->randomFloat(2, 0, 1000),
            'price' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
