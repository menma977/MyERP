<?php

namespace Database\Factories\Purchases;

use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseInvoiceFactory extends Factory
{
    protected $model = PurchaseInvoice::class;

    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 100, 10000);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'code' => $this->faker->unique()->bothify('INV-####-????'),
            'total' => $total,
            'tax' => $total * 0.12,
        ];
    }
}
