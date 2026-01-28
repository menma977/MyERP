<?php

namespace Tests\Feature\Sales;

use App\Enums\DiscountTypeEnum;
use App\Models\Permission;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sales_invoices(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());

        SalesInvoice::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.invoice.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total', 'tax', 'grand_total'],
                ],
            ]);
    }

    public function test_show_returns_sales_invoice(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.show')->where('guard_name', 'sanctum')->first());

        $invoice = SalesInvoice::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.sales.invoice.show', $invoice->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $invoice->id,
                    'code' => $invoice->code,
                ],
            ]);
    }

    public function test_update_updates_sales_invoice(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.update')->where('guard_name', 'sanctum')->first());

        $invoice = SalesInvoice::factory()->create();

        $array = [
            'total' => 1000,
            'tax' => 100,
            'discount_type' => DiscountTypeEnum::AMOUNT->value,
            'discount' => 50,
            'fee' => 10,
            'note' => 'Updated note',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.sales.invoice.update', $invoice->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_invoices', [
            'id' => $invoice->id,
            'total' => 1000,
            'tax' => 100,
            'discount' => 50,
            'fee' => 10,
            'grand_total' => 1060, // 1000 + 100 + 10 - 50
        ]);
    }

    public function test_approve_creates_ledger_entry(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'sales.invoice.approve')->where('guard_name', 'sanctum')->first());

        $invoice = SalesInvoice::factory()->create(['total' => 1000, 'paid' => 1000]);
        SalesInvoiceComponent::factory()->count(2)->create([
            'sales_invoice_id' => $invoice->id,
            'total' => 500,
        ]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.sales.invoice.approve', $invoice->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('ledgers', ['in' => 1000]);
        $this->assertDatabaseCount('ledger_components', 1);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.invoice.index',
            'sales.invoice.show',
            'sales.invoice.update',
            'sales.invoice.delete',
            'sales.invoice.restore',
            'sales.invoice.destroy',
            'sales.invoice.approve',
            'sales.invoice.reject',
            'sales.invoice.cancel',
            'sales.invoice.rollback',
            'sales.invoice.force',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'sales_invoice']);
        }
    }
}
