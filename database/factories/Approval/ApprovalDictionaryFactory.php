<?php

namespace Database\Factories\Approval;

use App\Models\Approval\ApprovalDictionary;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalDictionaryFactory extends Factory
{
    protected $model = ApprovalDictionary::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'name' => $this->faker->unique()->word().' Dictionary',
        ];
    }
}
