<?php

namespace Tests\Feature\Sales;

use App\Models\Permission;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
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
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.invoice.index',
            'sales.invoice.component.index',
            'sales.invoice.component.show',
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
