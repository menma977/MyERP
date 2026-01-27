<?php

namespace Tests\Feature\Sales;

use App\Models\Permission;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesReturnControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sales_returns(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());

        SalesReturn::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.return.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'sales_order_id', 'sales_invoice_id', 'total'],
                ],
            ]);
    }

    public function test_index_returns_collection_when_type_is_collection(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());

        SalesReturn::factory()->count(2)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.return.index', ['type' => 'collection']));

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_show_returns_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.show')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.return.show', $salesReturn->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $salesReturn->id,
                    'code' => $salesReturn->code,
                ],
            ]);
    }

    public function test_store_creates_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.store')->where('guard_name', 'sanctum')->first());

        $order = SalesOrder::factory()->create();
        $invoice = SalesInvoice::factory()->create(['sales_order_id' => $order->id]);

        $array = [
            'sales_order_id' => $order->id,
            'sales_invoice_id' => $invoice->id,
            'total' => 500.00,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_returns', [
            'sales_order_id' => $order->id,
            'sales_invoice_id' => $invoice->id,
            'total' => 500.00,
        ]);
    }

    public function test_update_updates_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.update')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();

        $array = ['total' => 750.00];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.sales.return.update', $salesReturn->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_returns', [
            'id' => $salesReturn->id,
            'total' => 750.00,
        ]);
    }

    public function test_delete_soft_deletes_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.delete')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.sales.return.delete', $salesReturn->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('sales_returns', ['id' => $salesReturn->id]);
    }

    public function test_restore_restores_soft_deleted_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.restore')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();
        $salesReturn->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.restore', $salesReturn->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_returns', [
            'id' => $salesReturn->id,
            'deleted_at' => null,
        ]);
    }

    public function test_destroy_force_deletes_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.destroy')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();
        $salesReturn->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.sales.return.destroy', $salesReturn->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sales_returns', ['id' => $salesReturn->id]);
    }

    public function test_approve_approves_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.store')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.approve', $salesReturn->id));

        $response->assertStatus(200);
    }

    public function test_reject_rejects_sales_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.store')->where('guard_name', 'sanctum')->first());

        $salesReturn = SalesReturn::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.reject', $salesReturn->id));

        $response->assertStatus(200);
    }

    public function test_store_validation_requires_order_and_invoice(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.return.store')->where('guard_name', 'sanctum')->first());

        $array = ['total' => 500.00];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.return.store'), $array);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sales_order_id', 'sales_invoice_id']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.return.index',
            'sales.return.show',
            'sales.return.store',
            'sales.return.update',
            'sales.return.delete',
            'sales.return.restore',
            'sales.return.destroy',
            'sales.return.approve',
            'sales.return.reject',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'sales_return']);
        }
    }
}
