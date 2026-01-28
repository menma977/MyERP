<?php

namespace Tests\Feature\Vendors;

use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendors(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permission */
        $permission = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permission);

        Vendor::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'name', 'address', 'phone', 'email'],
                ],
            ]);
    }

    public function test_show_returns_vendor(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permShow */
        $permShow = Permission::where('name', 'vendor.show')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permShow);

        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.show', $vendor->ulid));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $vendor->ulid,
                    'name' => $vendor->name,
                ],
            ]);
    }

    public function test_store_creates_vendor(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permStore */
        $permStore = Permission::where('name', 'vendor.store')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permStore);

        $array = Vendor::factory()->make()->toArray();
        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at'], $array['ulid']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.store'), $array);

        $response->assertStatus(200);

        $this->assertDatabaseHas('vendors', ['code' => $array['code'], 'name' => $array['name']]);
    }

    public function test_update_updates_vendor(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permUpdate */
        $permUpdate = Permission::where('name', 'vendor.update')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permUpdate);

        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        $newData = Vendor::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at'], $newData['ulid']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.update', $vendor->ulid), $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'name' => $newData['name']]);
    }

    public function test_delete_soft_deletes_vendor(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permDelete */
        $permDelete = Permission::where('name', 'vendor.delete')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permDelete);

        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.delete', $vendor->ulid));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendors', ['id' => $vendor->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.show',
            'vendor.store',
            'vendor.update',
            'vendor.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor']);
        }
    }
}
