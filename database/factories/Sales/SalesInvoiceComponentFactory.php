<?php

namespace Database\Factories\Sales;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesInvoiceComponentFactory extends Factory
{
    protected $model = SalesInvoiceComponent::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'sales_invoice_id' => SalesInvoice::factory(),
            'item_id' => Item::factory(),
            'item_batch_id' => ItemBatch::factory(),
            'item_stock_id' => ItemStock::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}
