<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\ApprovalComponent;
use App\Models\Approval\ApprovalContributor;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApprovalComponentContributorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_approval_contributors(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();
        ApprovalContributor::factory()->count(3)->create(['approval_component_id' => $component->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.component.contributor.index', [
            'approval_id' => $component->approval_id,
            'approval_component_id' => $component->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'component' => [
                            'id',
                        ],
                    ],
                ],
            ]);
    }

    public function test_show_returns_approval_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.show')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalContributor::factory()->create();
        $contributor->load('component');

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.component.contributor.show', [
            'approval_id' => $contributor->component->approval_id,
            'approval_component_id' => $contributor->approval_component_id,
            'id' => $contributor->id,
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $contributor->id,
                ],
            ]);
    }

    public function test_store_creates_approval_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.store')->where('guard_name', 'sanctum')->first());

        $component = ApprovalComponent::factory()->create();
        $targetUser = User::factory()->create();
        $array = [
            'approvable_id' => $targetUser->id,
            'key' => 'user',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.component.contributor.store', [
            'approval_id' => $component->approval->id,
            'approval_component_id' => $component->id,
        ]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_contributors', ['approvable_id' => $targetUser->id, 'approval_component_id' => $component->id]);
    }

    public function test_update_updates_approval_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.update')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalContributor::factory()->create();
        $contributor->load('component');

        $targetUser = User::factory()->create();
        $array = [
            'approvable_id' => $targetUser->id,
            'key' => 'user',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.component.contributor.update', [
            'approval_id' => $contributor->component->approval_id,
            'approval_component_id' => $contributor->approval_component_id,
            'id' => $contributor->id,
        ]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_contributors', ['id' => $contributor->id, 'approvable_id' => $targetUser->id]);
    }

    public function test_delete_soft_deletes_approval_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.delete')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalContributor::factory()->create();
        $contributor->load('component');

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.component.contributor.delete', [
            'approval_id' => $contributor->component->approval_id,
            'approval_component_id' => $contributor->approval_component_id,
            'id' => $contributor->id,
        ]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_contributors', ['id' => $contributor->id]);
    }

    public function test_restore_restores_approval_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.restore')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalContributor::factory()->create();
        $contributor->load('component');
        $contributor->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.component.contributor.restore', [
            'approval_id' => $contributor->component->approval_id,
            'approval_component_id' => $contributor->approval_component_id,
            'id' => $contributor->id,
        ]));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('approval_contributors', ['id' => $contributor->id]);
    }

    public function test_destroy_force_deletes_approval_contributor(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.component.contributor.destroy')->where('guard_name', 'sanctum')->first());

        $contributor = ApprovalContributor::factory()->create();
        $contributor->load('component');
        $contributor->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.component.contributor.destroy', [
            'approval_id' => $contributor->component->approval_id,
            'approval_component_id' => $contributor->approval_component_id,
            'id' => $contributor->id,
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('approval_contributors', ['id' => $contributor->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.component.index',
            'approval.component.contributor.index',
            'approval.component.contributor.show',
            'approval.component.contributor.store',
            'approval.component.contributor.update',
            'approval.component.contributor.delete',
            'approval.component.contributor.restore',
            'approval.component.contributor.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_contributor']);
        }
    }
}
