<?php

namespace Database\Factories\Transactions;

use App\Models\Transactions\Ledger;
use Illuminate\Database\Eloquent\Factories\Factory;

class LedgerFactory extends Factory
{
    protected $model = Ledger::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('LDGR-####-????'),
            'in' => $this->faker->randomFloat(2, 0, 10000),
            'out' => $this->faker->randomFloat(2, 0, 10000),
            'total' => $this->faker->randomFloat(2, -10000, 10000),
        ];
    }
}
