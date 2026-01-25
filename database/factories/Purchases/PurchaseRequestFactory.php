<?php

namespace Database\Factories\Purchases;

use App\Models\Purchases\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseRequestFactory extends Factory
{
    protected $model = PurchaseRequest::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('PR-####-????'),
            'total' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
