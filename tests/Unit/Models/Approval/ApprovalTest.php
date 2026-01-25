<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalComponent;
use App\Models\Approval\ApprovalFlow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_flow(): void
    {
        $flow = ApprovalFlow::factory()->create();
        $approval = Approval::factory()->create(['approval_flow_id' => $flow->id]);

        $this->assertInstanceOf(ApprovalFlow::class, $approval->flow);
        $this->assertEquals($flow->id, $approval->flow->id);
    }

    public function test_it_has_many_components(): void
    {
        $approval = Approval::factory()->create();
        $component = ApprovalComponent::factory()->create(['approval_id' => $approval->id]);

        $this->assertTrue($approval->components->contains($component));
    }
}
