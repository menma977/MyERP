<?php

namespace Database\Factories\Purchases;

use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseProcurementFactory extends Factory
{
    protected $model = PurchaseProcurement::class;

    public function definition(): array
    {
        return [
            'purchase_request_id' => PurchaseRequest::factory(),
            'code' => $this->faker->unique()->bothify('PROC-####-????'),
            'note' => $this->faker->sentence(),
        ];
    }
}
