<?php

namespace Database\Factories\Transactions;

use App\Models\Transactions\Ledger;
use App\Models\Transactions\LedgerComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class LedgerComponentFactory extends Factory
{
    protected $model = LedgerComponent::class;

    public function definition(): array
    {
        return [
            'ledger_id' => Ledger::factory(),
            'in' => $this->faker->randomFloat(2, 0, 1000),
            'out' => $this->faker->randomFloat(2, 0, 1000),
            'total' => $this->faker->randomFloat(2, -1000, 1000),
        ];
    }
}
