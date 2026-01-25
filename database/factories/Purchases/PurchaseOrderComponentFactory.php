<?php

namespace Database\Factories\Purchases;

use App\Models\Items\Item;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseProcurementComponent;
use App\Models\Purchases\PurchaseRequestComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderComponentFactory extends Factory
{
    protected $model = PurchaseOrderComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'purchase_request_component_id' => PurchaseRequestComponent::factory(),
            'purchase_procurement_component_id' => PurchaseProcurementComponent::factory(),
            'item_id' => Item::factory(),
            'request_quantity' => $quantity,
            'request_price' => $price,
            'request_total' => $quantity * $price,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
            'note' => $this->faker->sentence(),
        ];
    }
}
