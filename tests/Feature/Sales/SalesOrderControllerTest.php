<?php

namespace Tests\Feature\Sales;

use App\Models\Customer\Customer;
use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\Permission;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sales_orders(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());

        SalesOrder::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.order.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total'],
                ],
            ]);
    }

    public function test_show_returns_sales_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.show')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.order.show', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'code' => $order->code,
                ],
            ]);
    }

    public function test_update_updates_sales_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.update')->where('guard_name', 'sanctum')->first());

        $customer = Customer::factory()->create();
        $order = SalesOrder::factory()->create(['customer_id' => $customer->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.sales.order.update', $order->id), [
            'customer_id' => $customer->id,
            'total' => 2500,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_orders', [
            'id' => $order->id,
            'total' => 2500,
        ]);
    }

    public function test_delete_soft_deletes_sales_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.delete')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.sales.order.delete', $order->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('sales_orders', ['id' => $order->id]);
    }

    public function test_restore_restores_soft_deleted_sales_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.restore')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();
        $order->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.order.restore', $order->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_orders', [
            'id' => $order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_destroy_force_deletes_sales_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.destroy')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();
        $order->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.sales.order.destroy', $order->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sales_orders', ['id' => $order->id]);
    }

    public function test_approve_creates_sales_invoice_with_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.approve')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();
        $batch = ItemBatch::factory()->create([
            'item_id' => $item->id,
            'is_available' => true,
            'expired_at' => now()->addDays(30),
        ]);
        $stock = ItemStock::factory()->create([
            'item_batch_id' => $batch->id,
            'quantity' => 100,
        ]);

        $order = SalesOrder::factory()->create(['total' => 1000]);
        SalesOrderComponent::factory()->create([
            'sales_order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 50,
            'price' => 20,
            'total' => 1000,
        ]);

        $initialInvoiceCount = SalesInvoice::count();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.order.approve', $order->id));

        $response->assertStatus(200);
        $this->assertDatabaseCount('sales_invoices', $initialInvoiceCount + 1);

        $invoice = SalesInvoice::latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($order->id, $invoice->sales_order_id);
        $this->assertDatabaseHas('sales_invoice_components', [
            'sales_invoice_id' => $invoice->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_reject_rejects_sales_order(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.order.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.order.reject')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.order.reject', $order->id));

        $response->assertStatus(200);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.order.index',
            'sales.order.show',
            'sales.order.update',
            'sales.order.delete',
            'sales.order.restore',
            'sales.order.destroy',
            'sales.order.approve',
            'sales.order.reject',
            'sales.order.cancel',
            'sales.order.rollback',
            'sales.order.force',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'sales_order']);
        }
    }
}
