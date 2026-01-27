<?php

namespace Database\Factories\Purchases;

use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchases\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'purchase_request_id' => PurchaseRequest::factory(),
            'purchase_procurement_id' => PurchaseProcurement::factory(),
            'code' => $this->faker->unique()->bothify('PO-####-????'),
            'request_total' => $this->faker->randomFloat(2, 100, 10000),
            'total' => $this->faker->randomFloat(2, 100, 10000),
            'note' => $this->faker->sentence(),
        ];
    }
}
