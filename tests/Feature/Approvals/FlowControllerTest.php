<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\ApprovalFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class FlowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_flows(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());

        ApprovalFlow::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.flow.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);
    }

    public function test_show_returns_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.show')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.flow.show', $flow->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $flow->id,
                'name' => $flow->name,
            ]);
    }

    public function test_store_creates_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.store')->where('guard_name', 'sanctum')->first());

        $array = [
            'name' => 'New Approval Flow',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.flow.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_flows', ['name' => 'New Approval Flow']);
    }

    public function test_update_updates_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.update')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();
        $array = [
            'name' => 'Updated Flow Name',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.flow.update', $flow->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_flows', ['id' => $flow->id, 'name' => 'Updated Flow Name']);
    }

    public function test_delete_soft_deletes_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.delete')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.flow.delete', $flow->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_flows', ['id' => $flow->id]);
    }

    public function test_restore_restores_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.restore')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();
        $flow->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.flow.restore', $flow->id));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approval_flows', ['id' => $flow->id]);
    }

    public function test_destroy_force_deletes_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.destroy')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();
        $flow->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.flow.destroy', $flow->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approval_flows', ['id' => $flow->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.flow.index',
            'approval.flow.show',
            'approval.flow.store',
            'approval.flow.update',
            'approval.flow.delete',
            'approval.flow.restore',
            'approval.flow.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_flow']);
        }
    }
}
