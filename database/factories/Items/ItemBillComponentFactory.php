<?php

namespace Database\Factories\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBill;
use App\Models\Items\ItemBillComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\ItemBillComponent>
 */
class ItemBillComponentFactory extends Factory
{
    protected $model = ItemBillComponent::class;

    public function definition(): array
    {
        return [
            'item_bill_id' => ItemBill::factory(),
            'item_id' => Item::factory(),
            'quantity' => $this->faker->randomFloat(2, 0.1, 10),
        ];
    }
}
