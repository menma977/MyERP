<?php

namespace Tests\Feature\Vendors;

use App\Models\Items\Item;
use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_components(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permCompIndex */
        $permCompIndex = Permission::where('name', 'vendor.component.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompIndex);

        /** @var VendorComponent $component */
        $component = VendorComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.component.index', ['vendor_id' => $component->vendor_id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_id', 'item_id', 'price'],
                ],
            ]);
    }

    public function test_show_returns_vendor_component(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permCompIndex */
        $permCompIndex = Permission::where('name', 'vendor.component.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompIndex);
        /** @var Permission $permCompShow */
        $permCompShow = Permission::where('name', 'vendor.component.show')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompShow);

        /** @var VendorComponent $component */
        $component = VendorComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('api.v1.vendor.component.show', ['vendor_id' => $component->vendor_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_vendor_component(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permCompIndex */
        $permCompIndex = Permission::where('name', 'vendor.component.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompIndex);
        /** @var Permission $permCompStore */
        $permCompStore = Permission::where('name', 'vendor.component.store')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompStore);

        $array = VendorComponent::factory()->make()->toArray();
        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        /** @var Item $item */
        $item = Item::factory()->create();

        $array['vendor_id'] = $vendor->id;
        $array['item_id'] = $item->id;

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.component.store', ['vendor_id' => $vendor->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_components', ['vendor_id' => $array['vendor_id'], 'price' => $array['price']]);
    }

    public function test_update_updates_vendor_component(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permCompIndex */
        $permCompIndex = Permission::where('name', 'vendor.component.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompIndex);
        /** @var Permission $permCompUpdate */
        $permCompUpdate = Permission::where('name', 'vendor.component.update')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompUpdate);

        /** @var VendorComponent $component */
        $component = VendorComponent::factory()->create();
        $newData = VendorComponent::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        /** @var Item $item */
        $item = Item::factory()->create();
        $newData['vendor_id'] = $vendor->id;
        $newData['item_id'] = $item->id;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.component.update', ['vendor_id' => $component->vendor_id, 'id' => $component->id]), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_components', ['id' => $component->id, 'vendor_id' => $newData['vendor_id'], 'price' => $newData['price']]);
    }

    public function test_delete_soft_deletes_vendor_component(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permCompIndex */
        $permCompIndex = Permission::where('name', 'vendor.component.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompIndex);
        /** @var Permission $permCompDelete */
        $permCompDelete = Permission::where('name', 'vendor.component.delete')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permCompDelete);

        /** @var VendorComponent $component */
        $component = VendorComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.component.delete', ['vendor_id' => $component->vendor_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.component.index',
            'vendor.component.show',
            'vendor.component.store',
            'vendor.component.update',
            'vendor.component.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_component']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_component']);
        }
    }
}
