<?php

namespace Tests\Feature\Purchases;

use App\Models\Permission;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_orders(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());

        PurchaseOrder::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.order.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.show')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.order.show', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'code' => $order->code,
                ],
            ]);
    }

    public function test_update_updates_purchase_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.update')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();
        $request = PurchaseRequest::factory()->create();
        $procurement = PurchaseProcurement::factory()->create();

        $array = [
            'purchase_request_id' => $request->id,
            'purchase_procurement_id' => $procurement->id,
            'request_total' => 1000,
            'note' => 'Updated order note',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.purchase.order.update', $order->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id, 'note' => 'Updated order note']);
    }

    public function test_approve_creates_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.order.approve')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();
        PurchaseOrderComponent::factory()->count(2)->create(['purchase_order_id' => $order->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.order.approve', $order->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('good_receipts', ['purchase_order_id' => $order->id]);
        $this->assertDatabaseCount('good_receipt_components', 2);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.order.index',
            'purchase.order.show',
            'purchase.order.store',
            'purchase.order.update',
            'purchase.order.delete',
            'purchase.order.restore',
            'purchase.order.destroy',
            'purchase.order.approve',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_order']);
        }
    }
}
