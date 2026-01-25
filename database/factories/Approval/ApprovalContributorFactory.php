<?php

namespace Database\Factories\Approval;

use App\Models\Approval\ApprovalComponent;
use App\Models\Approval\ApprovalContributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalContributorFactory extends Factory
{
    protected $model = ApprovalContributor::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'approval_component_id' => ApprovalComponent::factory(),
            'approvable_type' => get_class($user),
            'approvable_id' => $user->id,
        ];
    }
}
