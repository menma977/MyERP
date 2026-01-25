<?php

namespace Tests\Feature\Sales;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SalesInvoiceComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sales_invoice_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.component.index')->where('guard_name', 'sanctum')->first());

        $invoice = SalesInvoice::factory()->create();
        SalesInvoiceComponent::factory()->count(3)->create(['sales_invoice_id' => $invoice->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.invoice.component.index', ['sales_invoice_id' => $invoice->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sales_invoice_id', 'item_id', 'total'],
                ],
            ]);
    }

    public function test_show_returns_sales_invoice_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.component.show')->where('guard_name', 'sanctum')->first());

        $component = SalesInvoiceComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.invoice.component.show', ['sales_invoice_id' => $component->sales_invoice_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    public function test_store_creates_sales_invoice_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.component.store')->where('guard_name', 'sanctum')->first());

        $invoice = SalesInvoice::factory()->create();
        $item = Item::factory()->create();
        $batch = ItemBatch::factory()->create(['item_id' => $item->id]);
        $stock = ItemStock::factory()->create(['item_batch_id' => $batch->id]);

        $array = [
            'sales_invoice_id' => $invoice->id,
            'item_id' => $item->id,
            'item_batch_id' => $batch->id,
            'item_stock_id' => $stock->id,
            'quantity' => 10,
            'price' => 100,
            'total' => 1000,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.invoice.component.store', ['sales_invoice_id' => $invoice->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_invoice_components', ['sales_invoice_id' => $invoice->id, 'total' => 1000]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.invoice.index',
            'sales.invoice.component.index',
            'sales.invoice.component.show',
            'sales.invoice.component.store',
            'sales.invoice.component.update',
            'sales.invoice.component.delete',
            'sales.invoice.component.restore',
            'sales.invoice.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'sales_invoice_component']);
        }
    }
}
