<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalFlow;
use App\Models\Approval\ApprovalFlowComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_one_approval(): void
    {
        $flow = ApprovalFlow::factory()->create();
        $approval = Approval::factory()->create(['approval_flow_id' => $flow->id]);

        $this->assertInstanceOf(Approval::class, $flow->approval);
        $this->assertEquals($approval->id, $flow->approval->id);
    }

    public function test_it_has_many_components(): void
    {
        $flow = ApprovalFlow::factory()->create();
        $component = ApprovalFlowComponent::factory()->create(['approval_flow_id' => $flow->id]);

        $this->assertTrue($flow->components->contains($component));
    }
}
