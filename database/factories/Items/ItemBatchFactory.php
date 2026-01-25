<?php

namespace Database\Factories\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\ItemBatch>
 */
class ItemBatchFactory extends Factory
{
    protected $model = ItemBatch::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'code' => $this->faker->unique()->bothify('BATCH-####-????'),
            'expired_at' => $this->faker->dateTimeBetween('+1 month', '+2 years'),
            'is_available' => 1,
        ];
    }
}
