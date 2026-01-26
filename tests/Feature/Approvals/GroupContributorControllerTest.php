<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\ApprovalGroup;
use App\Models\Approval\ApprovalGroupContributor;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupContributorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_group_contributors(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();
        ApprovalGroupContributor::factory()->count(3)->create(['approval_group_id' => $group->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.group.contributor.index', ['group_id' => $group->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'approval_group_id', 'user' => 'id'],
                ],
            ]);
    }

    public function test_show_returns_group_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.show')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalGroupContributor::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.group.contributor.show', ['group_id' => $contributor->approval_group_id, 'id' => $contributor->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $contributor->id,
                ],
            ]);
    }

    public function test_store_creates_group_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.store')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();
        $targetUser = User::factory()->create();
        $array = [
            'user_id' => $targetUser->id,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.group.contributor.store', ['group_id' => $group->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_group_contributors', ['approval_group_id' => $group->id, 'user_id' => $targetUser->id]);
    }

    public function test_update_updates_group_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.update')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalGroupContributor::factory()->create();
        $targetUser = User::factory()->create();
        $array = [
            'user_id' => $targetUser->id,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.group.contributor.update', ['group_id' => $contributor->approval_group_id, 'id' => $contributor->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_group_contributors', ['id' => $contributor->id, 'user_id' => $targetUser->id]);
    }

    public function test_delete_soft_deletes_group_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.delete')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalGroupContributor::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.group.contributor.delete', ['group_id' => $contributor->approval_group_id, 'id' => $contributor->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_group_contributors', ['id' => $contributor->id]);
    }

    public function test_restore_restores_group_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.restore')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalGroupContributor::factory()->create();
        $contributor->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.group.contributor.restore', ['group_id' => $contributor->approval_group_id, 'id' => $contributor->id]));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approval_group_contributors', ['id' => $contributor->id]);
    }

    public function test_destroy_force_deletes_group_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.contributor.destroy')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalGroupContributor::factory()->create();
        $contributor->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.group.contributor.destroy', ['group_id' => $contributor->approval_group_id, 'id' => $contributor->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approval_group_contributors', ['id' => $contributor->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.group.index',
            'approval.group.contributor.index',
            'approval.group.contributor.show',
            'approval.group.contributor.store',
            'approval.group.contributor.update',
            'approval.group.contributor.delete',
            'approval.group.contributor.restore',
            'approval.group.contributor.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_group_contributor']);
        }
    }
}
