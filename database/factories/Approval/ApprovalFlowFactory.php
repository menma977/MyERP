<?php

namespace Database\Factories\Approval;

use App\Models\Approval\ApprovalFlow;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalFlowFactory extends Factory
{
    protected $model = ApprovalFlow::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word().' Flow',
        ];
    }
}
