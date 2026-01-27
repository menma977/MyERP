<?php

namespace Database\Factories\Approval;

use App\Enums\ApprovalTypeEnum;
use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalFlow;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalFactory extends Factory
{
    protected $model = Approval::class;

    public function definition(): array
    {
        return [
            'approval_flow_id' => ApprovalFlow::factory(),
            'name' => $this->faker->unique()->word().' Approval',
            'type' => $this->faker->randomElement(ApprovalTypeEnum::cases()),
            'can_change' => $this->faker->boolean(),
        ];
    }
}
