<?php

namespace Tests\Feature\Sales;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\Sales\SalesReturn;
use App\Models\Sales\SalesReturnComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SalesReturnComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sales_return_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());

        $component = SalesReturnComponent::factory()->create();
        SalesReturnComponent::factory()->count(2)->create(['sales_return_id' => $component->sales_return_id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.return.component.index', ['sales_return_id' => $component->sales_return_id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sales_return_id', 'item_id', 'quantity', 'price', 'total'],
                ],
            ]);
    }

    public function test_show_returns_sales_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.show')->where('guard_name', 'sanctum')->first());

        $component = SalesReturnComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.return.component.show', ['sales_return_id' => $component->sales_return_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
                'sales_return_id' => $component->sales_return_id,
            ]);
    }

    public function test_store_creates_sales_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.store')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();
        $item = Item::factory()->create();
        $batch = ItemBatch::factory()->create(['item_id' => $item->id]);
        $stock = ItemStock::factory()->create(['item_batch_id' => $batch->id]);

        $array = [
            'sales_return_id' => $salesReturn->id,
            'item_id' => $item->id,
            'item_batch_id' => $batch->id,
            'item_stock_id' => $stock->id,
            'quantity' => 10,
            'price' => 50.00,
            'total' => 500.00,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.component.store', ['sales_return_id' => $salesReturn->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_return_components', [
            'sales_return_id' => $salesReturn->id,
            'item_id' => $item->id,
            'quantity' => 10,
        ]);
    }

    public function test_update_updates_sales_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.update')->where('guard_name', 'sanctum')->first());

        $component = SalesReturnComponent::factory()->create();

        $array = [
            'sales_return_id' => $component->sales_return_id,
            'item_id' => $component->item_id,
            'item_batch_id' => $component->item_batch_id,
            'item_stock_id' => $component->item_stock_id,
            'quantity' => 20,
            'price' => 75.00,
            'total' => 1500.00,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.sales.return.component.update', ['sales_return_id' => $component->sales_return_id, 'id' => $component->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_return_components', [
            'id' => $component->id,
            'quantity' => 20,
        ]);
    }

    public function test_delete_soft_deletes_sales_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.delete')->where('guard_name', 'sanctum')->first());

        $component = SalesReturnComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.sales.return.component.delete', ['sales_return_id' => $component->sales_return_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('sales_return_components', ['id' => $component->id]);
    }

    public function test_restore_restores_soft_deleted_sales_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.restore')->where('guard_name', 'sanctum')->first());

        $component = SalesReturnComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.component.restore', ['sales_return_id' => $component->sales_return_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_return_components', [
            'id' => $component->id,
            'deleted_at' => null,
        ]);
    }

    public function test_destroy_force_deletes_sales_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.destroy')->where('guard_name', 'sanctum')->first());

        $component = SalesReturnComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.sales.return.component.destroy', ['sales_return_id' => $component->sales_return_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sales_return_components', ['id' => $component->id]);
    }

    public function test_store_validation_requires_all_fields(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.component.store')->where('guard_name', 'sanctum')->first());

        $array = [];

        Sanctum::actingAs($user, ['*']);
        $salesReturn = SalesReturn::factory()->create();
        $response = $this->postJson(route('api.v1.sales.return.component.store', ['sales_return_id' => $salesReturn->id]), $array);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sales_return_id', 'item_id', 'quantity', 'price', 'total']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.return.index',
            'sales.return.component.index',
            'sales.return.component.show',
            'sales.return.component.store',
            'sales.return.component.update',
            'sales.return.component.delete',
            'sales.return.component.restore',
            'sales.return.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'sales_return_component']);
        }
    }
}
