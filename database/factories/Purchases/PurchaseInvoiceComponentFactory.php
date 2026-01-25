<?php

namespace Database\Factories\Purchases;

use App\Models\Items\Item;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrderComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseInvoiceComponentFactory extends Factory
{
    protected $model = PurchaseInvoiceComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'purchase_invoice_id' => PurchaseInvoice::factory(),
            'purchase_order_component_id' => PurchaseOrderComponent::factory(),
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}
