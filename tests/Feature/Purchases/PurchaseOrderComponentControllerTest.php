<?php

namespace Tests\Feature\Purchases;

use App\Models\Items\Item;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseProcurementComponent;
use App\Models\Purchases\PurchaseRequestComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PurchaseOrderComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_order_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.component.index')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();
        PurchaseOrderComponent::factory()->count(3)->create(['purchase_order_id' => $order->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.order.component.index', ['purchase_order_id' => $order->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'purchase_order_id', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_order_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.component.show')->where('guard_name', 'sanctum')->first());

        $component = PurchaseOrderComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.order.component.show', ['purchase_order_id' => $component->purchase_order_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    public function test_store_creates_purchase_order_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.component.store')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();
        $item = Item::factory()->create();
        $prComponent = PurchaseRequestComponent::factory()->create();
        $ppComponent = PurchaseProcurementComponent::factory()->create();

        $array = [
            'purchase_order_id' => $order->id,
            'purchase_request_component_id' => $prComponent->id,
            'purchase_procurement_component_id' => $ppComponent->id,
            'item_id' => $item->id,
            'request_quantity' => 10,
            'request_price' => 100,
            'quantity' => 10,
            'price' => 90,
            'note' => 'New order component',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.order.component.store', ['purchase_order_id' => $order->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_order_components', ['purchase_order_id' => $order->id, 'total' => 900]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.order.index',
            'purchase.order.component.index',
            'purchase.order.component.show',
            'purchase.order.component.store',
            'purchase.order.component.update',
            'purchase.order.component.delete',
            'purchase.order.component.restore',
            'purchase.order.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_order_component']);
        }
    }
}
