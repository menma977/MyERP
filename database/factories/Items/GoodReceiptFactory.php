<?php

namespace Database\Factories\Items;

use App\Models\Items\GoodReceipt;
use App\Models\Purchases\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\GoodReceipt>
 */
class GoodReceiptFactory extends Factory
{
    protected $model = GoodReceipt::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'code' => $this->faker->unique()->bothify('GR-####-????'),
            'total' => $this->faker->randomFloat(2, 100, 10000),
            'note' => $this->faker->sentence(),
        ];
    }
}
