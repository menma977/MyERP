<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\ApprovalGroup;
use App\Models\Approval\ApprovalGroupContributor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalGroupContributorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_group(): void
    {
        $group = ApprovalGroup::factory()->create();
        $contributor = ApprovalGroupContributor::factory()->create(['approval_group_id' => $group->id]);

        $this->assertInstanceOf(ApprovalGroup::class, $contributor->group);
        $this->assertEquals($group->id, $contributor->group->id);
    }

    public function test_it_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $contributor = ApprovalGroupContributor::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $contributor->user);
        $this->assertEquals($user->id, $contributor->user->id);
    }
}
