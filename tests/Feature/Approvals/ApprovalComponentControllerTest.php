<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApprovalComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_approval_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();
        ApprovalComponent::factory()->count(3)->create(['approval_id' => $approval->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.component.index', ['approval_id' => $approval->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'approval_id', 'name', 'step', 'type', 'color'],
                ],
            ]);
    }

    public function test_show_returns_approval_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.show')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.component.show', ['approval_id' => $component->approval_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
                'name' => $component->name,
            ]);
    }

    public function test_store_creates_approval_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.store')->where('guard_name', 'sanctum')->first());

        $approval = Approval::factory()->create();
        $array = [
            'name' => 'New Component',
            'type' => 1,
            'color' => '#ffffff',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.component.store', ['approval_id' => $approval->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_components', ['name' => 'New Component', 'approval_id' => $approval->id]);
    }

    public function test_update_updates_approval_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.update')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();
        $array = [
            'name' => 'Updated Component Name',
            'type' => 0,
            'color' => '#000000',
            'step' => 5,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.component.update', ['approval_id' => $component->approval_id, 'id' => $component->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_components', ['id' => $component->id, 'name' => 'Updated Component Name', 'step' => 0]); // Synchronize steps might reset it if it's the only one
    }

    public function test_delete_soft_deletes_approval_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.delete')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.component.delete', ['approval_id' => $component->approval_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_components', ['id' => $component->id]);
    }

    public function test_restore_restores_approval_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.restore')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.component.restore', ['approval_id' => $component->approval_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approval_components', ['id' => $component->id]);
    }

    public function test_destroy_force_deletes_approval_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.destroy')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.component.destroy', ['approval_id' => $component->approval_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approval_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.component.index',
            'approval.component.show',
            'approval.component.store',
            'approval.component.update',
            'approval.component.delete',
            'approval.component.restore',
            'approval.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_component']);
        }
    }
}
