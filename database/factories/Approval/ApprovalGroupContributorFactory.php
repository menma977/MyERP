<?php

namespace Database\Factories\Approval;

use App\Models\Approval\ApprovalGroup;
use App\Models\Approval\ApprovalGroupContributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalGroupContributorFactory extends Factory
{
    protected $model = ApprovalGroupContributor::class;

    public function definition(): array
    {
        return [
            'approval_group_id' => ApprovalGroup::factory(),
            'user_id' => User::factory(),
        ];
    }
}
