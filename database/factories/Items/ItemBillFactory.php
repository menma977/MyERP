<?php

namespace Database\Factories\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\ItemBill>
 */
class ItemBillFactory extends Factory
{
    protected $model = ItemBill::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'code' => $this->faker->unique()->bothify('BILL-####-????'),
            'quantity' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
