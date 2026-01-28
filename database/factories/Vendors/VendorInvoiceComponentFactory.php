<?php

namespace Database\Factories\Vendors;

use App\Models\Items\Item;
use App\Models\Vendors\VendorComponent;
use App\Models\Vendors\VendorInvoice;
use App\Models\Vendors\VendorInvoiceComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorInvoiceComponentFactory extends Factory
{
    protected $model = VendorInvoiceComponent::class;

    public function definition(): array
    {
        return [
            'vendor_invoice_id' => VendorInvoice::factory(),
            'vendor_component_id' => VendorComponent::factory(),
            'item_id' => Item::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
