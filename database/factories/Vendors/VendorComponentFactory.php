<?php

namespace Database\Factories\Vendors;

use App\Models\Items\Item;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorComponentFactory extends Factory
{
    protected $model = VendorComponent::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'item_id' => Item::factory(),
            'price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
