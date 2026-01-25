<?php

namespace Tests\Feature\Approvals;

use App\Models\Approval\ApprovalDictionary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DictionaryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_dictionaries(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.index')->where('guard_name', 'sanctum')->first());

        ApprovalDictionary::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.dictionary.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'key', 'name'],
                ],
            ]);
    }

    public function test_show_returns_dictionary(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.show')->where('guard_name', 'sanctum')->first());

        $dictionary = ApprovalDictionary::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.approval.dictionary.show', $dictionary->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $dictionary->id,
                'name' => $dictionary->name,
            ]);
    }

    public function test_store_creates_dictionary(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.store')->where('guard_name', 'sanctum')->first());

        $array = [
            'key' => 'new_key',
            'name' => 'New Dictionary',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.approval.dictionary.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_dictionaries', ['key' => 'new_key', 'name' => 'New Dictionary']);
    }

    public function test_update_updates_dictionary(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.update')->where('guard_name', 'sanctum')->first());

        $dictionary = ApprovalDictionary::factory()->create();
        $array = [
            'key' => 'updated_key',
            'name' => 'Updated Dictionary Name',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.approval.dictionary.update', $dictionary->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('approval_dictionaries', ['id' => $dictionary->id, 'key' => 'updated_key', 'name' => 'Updated Dictionary Name']);
    }

    public function test_delete_soft_deletes_dictionary(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'approval.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'approval.dictionary.delete')->where('guard_name', 'sanctum')->first());

        $dictionary = ApprovalDictionary::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.approval.dictionary.delete', $dictionary->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('approval_dictionaries', ['id' => $dictionary->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'approval.index',
            'approval.dictionary.index',
            'approval.dictionary.show',
            'approval.dictionary.store',
            'approval.dictionary.update',
            'approval.dictionary.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'approval_dictionary']);
        }
    }
}
