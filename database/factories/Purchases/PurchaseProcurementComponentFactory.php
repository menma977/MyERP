<?php

namespace Database\Factories\Purchases;

use App\Models\Items\Item;
use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseProcurementComponent;
use App\Models\Vendors\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseProcurementComponentFactory extends Factory
{
    protected $model = PurchaseProcurementComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'purchase_procurement_id' => PurchaseProcurement::factory(),
            'item_id' => Item::factory(),
            'vendor_id' => Vendor::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
            'note' => $this->faker->sentence(),
        ];
    }
}
