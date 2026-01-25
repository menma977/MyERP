<?php

namespace Database\Factories\Transactions;

use App\Enums\PaymentMethodEnum;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Transactions\PaymentRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentRequestFactory extends Factory
{
    protected $model = PaymentRequest::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'purchase_invoice_id' => PurchaseInvoice::factory(),
            'code' => $this->faker->unique()->bothify('PYR-####-????'),
            'method' => $this->faker->randomElement(PaymentMethodEnum::cases()),
            'total' => $this->faker->randomFloat(2, 100, 10000),
            'tax' => $this->faker->randomFloat(2, 10, 1000),
            'note' => $this->faker->sentence(),
        ];
    }
}
