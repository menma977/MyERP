<?php

namespace Database\Factories\Purchases;

use App\Models\Items\Item;
use App\Models\Purchases\PurchaseRequest;
use App\Models\Purchases\PurchaseRequestComponent;
use App\Models\Vendors\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseRequestComponentFactory extends Factory
{
    protected $model = PurchaseRequestComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'purchase_request_id' => PurchaseRequest::factory(),
            'item_id' => Item::factory(),
            'vendor_id' => Vendor::factory(),
            'price' => $price,
            'quantity' => $quantity,
            'total' => $quantity * $price,
            'note' => $this->faker->sentence(),
        ];
    }
}
