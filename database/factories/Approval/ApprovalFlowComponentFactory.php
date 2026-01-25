<?php

namespace Database\Factories\Approval;

use App\Models\Approval\ApprovalDictionary;
use App\Models\Approval\ApprovalFlow;
use App\Models\Approval\ApprovalFlowComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalFlowComponentFactory extends Factory
{
    protected $model = ApprovalFlowComponent::class;

    public function definition(): array
    {
        return [
            'approval_flow_id' => ApprovalFlow::factory(),
            'approval_dictionary_id' => ApprovalDictionary::factory(),
            'key' => $this->faker->unique()->word(),
        ];
    }
}
