<?php

namespace Tests\Feature\Purchases;

use App\Models\Items\Item;
use App\Models\Permission;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseInvoiceComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_invoice_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());

        $invoice = PurchaseInvoice::factory()->create();
        PurchaseInvoiceComponent::factory()->count(3)->create(['purchase_invoice_id' => $invoice->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.invoice.component.index', ['purchase_invoice_id' => $invoice->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'purchase_invoice_id', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_invoice_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.show')->where('guard_name', 'sanctum')->first());

        $component = PurchaseInvoiceComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.invoice.component.show', ['purchase_invoice_id' => $component->purchase_invoice_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_purchase_invoice_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.store')->where('guard_name', 'sanctum')->first());

        $invoice = PurchaseInvoice::factory()->create();
        $poComponent = PurchaseOrderComponent::factory()->create();
        $item = Item::factory()->create();

        $array = [
            'purchase_invoice_id' => $invoice->id,
            'purchase_order_component_id' => $poComponent->id,
            'item_id' => $item->id,
            'quantity' => 10,
            'price' => 100,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.invoice.component.store', ['purchase_invoice_id' => $invoice->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_invoice_components', ['purchase_invoice_id' => $invoice->id, 'total' => 1000]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.invoice.index',
            'purchase.component.index',
            'purchase.component.show',
            'purchase.component.store',
            'purchase.component.update',
            'purchase.component.delete',
            'purchase.component.restore',
            'purchase.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_invoice_component']);
        }
    }
}
