<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\ApprovalGroup;
use App\Models\Approval\ApprovalGroupContributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_contributors(): void
    {
        $group = ApprovalGroup::factory()->create();
        $contributor = ApprovalGroupContributor::factory()->create(['approval_group_id' => $group->id]);

        $this->assertTrue($group->contributors->contains($contributor));
        $this->assertInstanceOf(ApprovalGroupContributor::class, $group->contributors->first());
    }
}
