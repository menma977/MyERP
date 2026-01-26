<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\ApprovalDictionary;
use App\Models\Approval\ApprovalFlow;
use App\Models\Approval\ApprovalFlowComponent;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FlowComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_flow_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();
        ApprovalFlowComponent::factory()->count(3)->create(['approval_flow_id' => $flow->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.flow.component.index', ['flow_id' => $flow->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'approval_flow_id', 'approval_dictionary_id', 'key'],
                ],
            ]);
    }

    public function test_show_returns_flow_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.show')->where('guard_name', 'sanctum')->first());

        $component = ApprovalFlowComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.flow.component.show', ['flow_id' => $component->approval_flow_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                    'key' => $component->key,
                ],
            ]);
    }

    public function test_store_creates_flow_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.store')->where('guard_name', 'sanctum')->first());

        $flow = ApprovalFlow::factory()->create();
        $dictionary = ApprovalDictionary::factory()->create();
        $array = [
            'approval_dictionary_id' => $dictionary->id,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.flow.component.store', ['flow_id' => $flow->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_flow_components', ['approval_flow_id' => $flow->id, 'approval_dictionary_id' => $dictionary->id, 'key' => $dictionary->key]);
    }

    public function test_update_updates_flow_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.update')->where('guard_name', 'sanctum')->first());

        $component = ApprovalFlowComponent::factory()->create();
        $newDictionary = ApprovalDictionary::factory()->create();
        $array = [
            'approval_dictionary_id' => $newDictionary->id,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.flow.component.update', ['flow_id' => $component->approval_flow_id, 'id' => $component->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_flow_components', ['id' => $component->id, 'approval_dictionary_id' => $newDictionary->id, 'key' => $newDictionary->key]);
    }

    public function test_delete_soft_deletes_flow_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.delete')->where('guard_name', 'sanctum')->first());

        $component = ApprovalFlowComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.flow.component.delete', ['flow_id' => $component->approval_flow_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_flow_components', ['id' => $component->id]);
    }

    public function test_restore_restores_flow_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.restore')->where('guard_name', 'sanctum')->first());

        $component = ApprovalFlowComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.flow.component.restore', ['flow_id' => $component->approval_flow_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approval_flow_components', ['id' => $component->id]);
    }

    public function test_destroy_force_deletes_flow_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.flow.component.destroy')->where('guard_name', 'sanctum')->first());

        $component = ApprovalFlowComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.flow.component.destroy', ['flow_id' => $component->approval_flow_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approval_flow_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.flow.index',
            'approval.flow.component.index',
            'approval.flow.component.show',
            'approval.flow.component.store',
            'approval.flow.component.update',
            'approval.flow.component.delete',
            'approval.flow.component.restore',
            'approval.flow.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_flow_component']);
        }
    }
}
