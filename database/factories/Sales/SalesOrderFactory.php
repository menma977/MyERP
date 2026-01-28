<?php

namespace Database\Factories\Sales;

use App\Models\Customer\Customer;
use App\Models\Sales\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'code' => $this->faker->unique()->bothify('SO-####-????'),
            'total' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
