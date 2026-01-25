<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalComponent;
use App\Models\Approval\ApprovalContributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_approval(): void
    {
        $approval = Approval::factory()->create();
        $component = ApprovalComponent::factory()->create(['approval_id' => $approval->id]);

        $this->assertInstanceOf(Approval::class, $component->approval);
        $this->assertEquals($approval->id, $component->approval->id);
    }

    public function test_it_has_many_contributors(): void
    {
        $component = ApprovalComponent::factory()->create();
        $contributor = ApprovalContributor::factory()->create(['approval_component_id' => $component->id]);

        $this->assertTrue($component->contributors->contains($contributor));
    }
}
