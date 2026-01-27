<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\ApprovalComponent;
use App\Models\Approval\ApprovalContributor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalContributorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_component(): void
    {
        $component = ApprovalComponent::factory()->create();
        $contributor = ApprovalContributor::factory()->create(['approval_component_id' => $component->id]);

        $this->assertInstanceOf(ApprovalComponent::class, $contributor->component);
        $this->assertEquals($component->id, $contributor->component->id);
    }

    public function test_it_morphs_to_approvable(): void
    {
        $user = User::factory()->create();
        $contributor = ApprovalContributor::factory()->create([
            'approvable_type' => get_class($user),
            'approvable_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $contributor->approvable);
        $this->assertEquals($user->id, $contributor->approvable->id);
    }
}
