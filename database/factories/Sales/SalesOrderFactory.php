<?php

namespace Database\Factories\Sales;

use App\Models\Sales\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('SO-####-????'),
            'total' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
