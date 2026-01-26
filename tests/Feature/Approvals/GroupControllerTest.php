<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\ApprovalGroup;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_groups(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());

        ApprovalGroup::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.group.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);
    }

    public function test_show_returns_group(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.show')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.group.show', $group->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $group->id,
                    'name' => $group->name,
                ],
            ]);
    }

    public function test_store_creates_group(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.store')->where('guard_name', 'sanctum')->first());

        $array = [
            'name' => 'New Approval Group',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.group.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_groups', ['name' => 'New Approval Group']);
    }

    public function test_update_updates_group(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.update')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();
        $array = [
            'name' => 'Updated Group Name',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.group.update', $group->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_groups', ['id' => $group->id, 'name' => 'Updated Group Name']);
    }

    public function test_delete_soft_deletes_group(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.delete')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.group.delete', $group->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_groups', ['id' => $group->id]);
    }

    public function test_restore_restores_group(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.restore')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();
        $group->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.group.restore', $group->id));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approval_groups', ['id' => $group->id]);
    }

    public function test_destroy_force_deletes_group(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.group.destroy')->where('guard_name', 'sanctum')->first());

        $group = ApprovalGroup::factory()->create();
        $group->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.group.destroy', $group->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approval_groups', ['id' => $group->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.group.index',
            'approval.group.show',
            'approval.group.store',
            'approval.group.update',
            'approval.group.delete',
            'approval.group.restore',
            'approval.group.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_group']);
        }
    }
}
