<?php

namespace Database\Factories\Approval;

use App\Enums\ContributorTypeEnum;
use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalComponentFactory extends Factory
{
    protected $model = ApprovalComponent::class;

    public function definition(): array
    {
        return [
            'approval_id' => Approval::factory(),
            'name' => $this->faker->word(),
            'step' => $this->faker->numberBetween(0, 10),
            'type' => $this->faker->randomElement(ContributorTypeEnum::cases()),
            'color' => $this->faker->safeColorName(),
            'can_drag' => true,
            'can_edit' => true,
            'can_delete' => true,
        ];
    }
}
