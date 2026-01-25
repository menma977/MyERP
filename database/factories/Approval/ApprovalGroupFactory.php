<?php

namespace Database\Factories\Approval;

use App\Models\Approval\ApprovalGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalGroupFactory extends Factory
{
    protected $model = ApprovalGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word().' Group',
        ];
    }
}
