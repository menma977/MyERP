<?php

namespace Database\Factories\Sales;

use App\Enums\DiscountTypeEnum;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesInvoiceFactory extends Factory
{
    protected $model = SalesInvoice::class;

    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 100, 10000);

        return [
            'sales_order_id' => SalesOrder::factory(),
            'code' => $this->faker->unique()->bothify('SI-####-????'),
            'total' => $total,
            'tax' => $total * 0.12,
            'discount_type' => $this->faker->randomElement(DiscountTypeEnum::cases()),
            'discount' => 0,
            'fee' => 0,
            'grand_total' => $total * 1.12,
        ];
    }
}
