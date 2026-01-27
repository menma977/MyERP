<?php

namespace Database\Factories\Purchases;

use App\Models\Items\GoodReceipt;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseReturnFactory extends Factory
{
    protected $model = PurchaseReturn::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'good_receipt_id' => GoodReceipt::factory(),
            'code' => $this->faker->unique()->bothify('RET-####-????'),
            'total' => $this->faker->randomFloat(2, 0, 10000),
            'note' => $this->faker->sentence(),
        ];
    }
}
