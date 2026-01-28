<?php

namespace Database\Factories\Vendors;

use App\Enums\PaymentMethodEnum;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorPaymentFactory extends Factory
{
    protected $model = VendorPayment::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'vendor_account_payable_id' => VendorAccountPayable::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'method' => PaymentMethodEnum::BANK_TRANSFER,
            'note' => $this->faker->sentence(),
            'paid_at' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
        ];
    }
}
