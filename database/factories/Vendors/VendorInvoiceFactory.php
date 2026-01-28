<?php

namespace Database\Factories\Vendors;

use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorInvoiceFactory extends Factory
{
    protected $model = VendorInvoice::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'code' => $this->faker->unique()->bothify('INV-####'),
            'total' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
