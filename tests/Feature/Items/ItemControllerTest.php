<?php

namespace Tests\Feature\Items;

use App\Models\Items\Item;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use UnitEnum;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_items()
    {
        $user = User::factory()->create();
        $permission = Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first();
        $user->givePermissionTo($permission);

        Item::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'name', 'type', 'unit', 'cost'],
                ],
            ]);
    }

    public function test_show_returns_item()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.show')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.show', $item->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                ],
            ]);
    }

    public function test_store_creates_item()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.store')->where('guard_name', 'sanctum')->first());

        $array = Item::factory()->make()->toArray();
        if (isset($array['type']) && $array['type'] instanceof UnitEnum) {
            $array['type'] = $array['type']->value;
        }
        if (isset($array['unit']) && $array['unit'] instanceof UnitEnum) {
            $array['unit'] = $array['unit']->value;
        }

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at'], $array['cost']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.store'), $array);

        $response->assertStatus(200);

        $this->assertDatabaseHas('items', ['code' => $array['code']]);
    }

    public function test_update_updates_item()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.update')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();
        $newData = Item::factory()->make()->toArray();
        if (isset($newData['type']) && $newData['type'] instanceof UnitEnum) {
            $newData['type'] = $newData['type']->value;
        }
        if (isset($newData['unit']) && $newData['unit'] instanceof UnitEnum) {
            $newData['unit'] = $newData['unit']->value;
        }

        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at'], $newData['cost']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.item.update', $item->id), $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => $newData['name']]);
    }

    public function test_delete_soft_deletes_item()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.delete')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.delete', $item->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_destroy_force_deletes_item()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.destroy')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();
        $item->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.destroy', $item->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_restore_restores_item()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.restore')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();
        $item->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.restore', $item->id));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('items', ['id' => $item->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'item.index',
            'item.show',
            'item.store',
            'item.update',
            'item.delete',
            'item.destroy',
            'item.restore',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'item']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'item']);
        }
    }
}
