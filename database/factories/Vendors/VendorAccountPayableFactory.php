<?php

namespace Database\Factories\Vendors;

use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorAccountPayableFactory extends Factory
{
    protected $model = VendorAccountPayable::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'vendor_invoice_id' => VendorInvoice::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'note' => $this->faker->sentence(),
        ];
    }
}
