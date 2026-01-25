<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApprovalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_approvals(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());

        Approval::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'approval_flow_id', 'name', 'type'],
                ],
            ]);
    }

    public function test_show_returns_approval(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.show')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.show', $approval->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $approval->id,
                'name' => $approval->name,
            ]);
    }

    public function test_store_creates_approval(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.store')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();
        $array = [
            'flow_id' => $flow->id,
            'name' => 'New Approval',
            'type' => 1,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approvals', ['name' => 'New Approval']);
    }

    public function test_update_updates_approval(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.update')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();
        $array = [
            'flow_id' => $approval->approval_flow_id,
            'name' => 'Updated Approval Name',
            'type' => 0,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.update', $approval->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approvals', ['id' => $approval->id, 'name' => 'Updated Approval Name']);
    }

    public function test_delete_soft_deletes_approval(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.delete')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.delete', $approval->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approvals', ['id' => $approval->id]);
    }

    public function test_restore_restores_approval(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.restore')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();
        $approval->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.restore', $approval->id));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approvals', ['id' => $approval->id]);
    }

    public function test_destroy_force_deletes_approval(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.destroy')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();
        $approval->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.destroy', $approval->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approvals', ['id' => $approval->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.show',
            'approval.store',
            'approval.update',
            'approval.delete',
            'approval.restore',
            'approval.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval']);
        }
    }
}
