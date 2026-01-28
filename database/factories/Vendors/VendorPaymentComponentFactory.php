<?php

namespace Database\Factories\Vendors;

use App\Models\Vendors\VendorAccountPayableComponent;
use App\Models\Vendors\VendorPayment;
use App\Models\Vendors\VendorPaymentComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorPaymentComponentFactory extends Factory
{
    protected $model = VendorPaymentComponent::class;

    public function definition(): array
    {
        return [
            'vendor_payment_id' => VendorPayment::factory(),
            'vendor_account_payable_component_id' => VendorAccountPayableComponent::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 50),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'total' => $this->faker->randomFloat(2, 5, 5000),
        ];
    }
}
