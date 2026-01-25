<?php

namespace Tests\Feature\Purchases;

use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PurchaseInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_invoices(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());

        PurchaseInvoice::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.invoice.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total', 'tax'],
                ],
            ]);
    }

    public function test_show_returns_purchase_invoice(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.show')->where('guard_name', 'sanctum')->first());

        $invoice = PurchaseInvoice::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.invoice.show', $invoice->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $invoice->id,
                'code' => $invoice->code,
            ]);
    }

    public function test_update_updates_purchase_invoice(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.update')->where('guard_name', 'sanctum')->first());

        $invoice = PurchaseInvoice::factory()->create();
        $newOrder = PurchaseOrder::factory()->create();

        $array = [
            'purchase_order_id' => $newOrder->id,
            'tax' => 120,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.purchase.invoice.update', $invoice->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_invoices', ['id' => $invoice->id, 'purchase_order_id' => $newOrder->id]);
    }

    public function test_approve_creates_payment_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.store')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();
        $invoice = PurchaseInvoice::factory()->create([
            'purchase_order_id' => $order->id,
            'total' => 1000,
            'tax' => 120,
        ]);
        PurchaseOrderComponent::factory()->count(2)->create([
            'purchase_order_id' => $order->id,
        ])->each(function ($orderComponent) use ($invoice) {
            PurchaseInvoiceComponent::factory()->create([
                'purchase_invoice_id' => $invoice->id,
                'purchase_order_component_id' => $orderComponent->id,
                'total' => 500,
            ]);
        });

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.invoice.approve', $invoice->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_requests', [
            'purchase_invoice_id' => $invoice->id,
            'total' => 1000,
            'tax' => 120,
        ]);
        $this->assertDatabaseCount('payment_request_components', 2);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.invoice.index',
            'purchase.invoice.show',
            'purchase.invoice.store',
            'purchase.invoice.update',
            'purchase.invoice.delete',
            'purchase.invoice.restore',
            'purchase.invoice.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_invoice']);
        }
    }
}
