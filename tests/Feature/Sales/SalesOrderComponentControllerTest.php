<?php

namespace Tests\Feature\Sales;

use App\Models\Items\Item;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SalesOrderComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sales_order_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.component.index')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();
        SalesOrderComponent::factory()->count(3)->create(['sales_order_id' => $order->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.order.component.index', ['sales_order_id' => $order->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sales_order_id', 'item_id', 'quantity', 'price', 'total'],
                ],
            ]);
    }

    public function test_show_returns_sales_order_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.component.show')->where('guard_name', 'sanctum')->first());

        $component = SalesOrderComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.order.component.show', ['sales_order_id' => $component->sales_order_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    public function test_store_creates_sales_order_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.component.store')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();
        $item = Item::factory()->create();

        $array = [
            'sales_order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 10,
            'price' => 100,
            'total' => 1000,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.order.component.store', ['sales_order_id' => $order->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_order_components', ['sales_order_id' => $order->id, 'item_id' => $item->id, 'total' => 1000]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.order.index',
            'sales.order.component.index',
            'sales.order.component.show',
            'sales.order.component.store',
            'sales.order.component.update',
            'sales.order.component.delete',
            'sales.order.component.restore',
            'sales.order.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'sales_order_component']);
        }
    }
}
