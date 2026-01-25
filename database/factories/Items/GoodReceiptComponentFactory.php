<?php

namespace Database\Factories\Items;

use App\Models\Items\GoodReceipt;
use App\Models\Items\GoodReceiptComponent;
use App\Models\Items\Item;
use App\Models\Purchases\PurchaseOrderComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\GoodReceiptComponent>
 */
class GoodReceiptComponentFactory extends Factory
{
    protected $model = GoodReceiptComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'good_receipt_id' => GoodReceipt::factory(),
            'purchase_order_component_id' => PurchaseOrderComponent::factory(),
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
            'expired_at' => $this->faker->dateTimeBetween('+1 month', '+2 years'),
        ];
    }
}
