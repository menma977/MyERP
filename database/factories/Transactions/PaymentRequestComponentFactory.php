<?php

namespace Database\Factories\Transactions;

use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Transactions\PaymentRequest;
use App\Models\Transactions\PaymentRequestComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentRequestComponentFactory extends Factory
{
    protected $model = PaymentRequestComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'payment_request_id' => PaymentRequest::factory(),
            'purchase_order_component_id' => PurchaseOrderComponent::factory(),
            'purchase_invoice_component_id' => PurchaseInvoiceComponent::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
            'note' => $this->faker->sentence(),
        ];
    }
}
