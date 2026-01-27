<?php

namespace Database\Factories\Sales;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesReturn>
 */
class SalesReturnFactory extends Factory
{
    protected $model = SalesReturn::class;

    public function definition(): array
    {
        return [
            'sales_order_id' => SalesOrder::factory(),
            'sales_invoice_id' => SalesInvoice::factory(),
            'code' => 'SR-'.fake()->unique()->numerify('######'),
            'total' => fake()->randomFloat(2, 100, 10000),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
