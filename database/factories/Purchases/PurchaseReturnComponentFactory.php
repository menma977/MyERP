<?php

namespace Database\Factories\Purchases;

use App\Models\Items\GoodReceiptComponent;
use App\Models\Items\Item;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseReturn;
use App\Models\Purchases\PurchaseReturnComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseReturnComponentFactory extends Factory
{
    protected $model = PurchaseReturnComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'purchase_return_id' => PurchaseReturn::factory(),
            'purchase_order_component_id' => PurchaseOrderComponent::factory(),
            'good_receipt_component_id' => GoodReceiptComponent::factory(),
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
            'note' => $this->faker->sentence(),
        ];
    }
}
