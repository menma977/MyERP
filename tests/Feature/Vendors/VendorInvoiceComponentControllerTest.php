<?php

namespace Tests\Feature\Vendors;

use App\Models\Items\Item;
use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\VendorComponent;
use App\Models\Vendors\VendorInvoice;
use App\Models\Vendors\VendorInvoiceComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorInvoiceComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_invoice_components()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.index')->where('guard_name', 'sanctum')->first());

        $component = VendorInvoiceComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.invoice.component.index', ['vendor_invoice_id' => $component->vendor_invoice_id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_invoice_id', 'vendor_component_id', 'item_id', 'quantity', 'price', 'total'],
                ],
            ]);
    }

    public function test_show_returns_vendor_invoice_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.show')->where('guard_name', 'sanctum')->first());

        $component = VendorInvoiceComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.invoice.component.show', ['vendor_invoice_id' => $component->vendor_invoice_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_vendor_invoice_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.store')->where('guard_name', 'sanctum')->first());

        $array = VendorInvoiceComponent::factory()->make()->toArray();
        $invoice = VendorInvoice::factory()->create();
        $vendorComponent = VendorComponent::factory()->create();
        $item = Item::factory()->create();

        $array['vendor_invoice_id'] = $invoice->id;
        $array['vendor_component_id'] = $vendorComponent->id;
        $array['item_id'] = $item->id;

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.invoice.component.store', ['vendor_invoice_id' => $invoice->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_invoice_components', ['vendor_invoice_id' => $array['vendor_invoice_id'], 'quantity' => $array['quantity']]);
    }

    public function test_update_updates_vendor_invoice_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.update')->where('guard_name', 'sanctum')->first());

        $component = VendorInvoiceComponent::factory()->create();
        $newData = VendorInvoiceComponent::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        $invoice = VendorInvoice::factory()->create();
        $vendorComponent = VendorComponent::factory()->create();
        $item = Item::factory()->create();
        $newData['vendor_invoice_id'] = $invoice->id;
        $newData['vendor_component_id'] = $vendorComponent->id;
        $newData['item_id'] = $item->id;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.invoice.component.update', ['vendor_invoice_id' => $component->vendor_invoice_id, 'id' => $component->id]), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_invoice_components', ['id' => $component->id, 'vendor_invoice_id' => $newData['vendor_invoice_id'], 'quantity' => $newData['quantity']]);
    }

    public function test_delete_soft_deletes_vendor_invoice_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.invoice.component.delete')->where('guard_name', 'sanctum')->first());

        $component = VendorInvoiceComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.invoice.component.delete', ['vendor_invoice_id' => $component->vendor_invoice_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_invoice_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.invoice.index',
            'vendor.invoice.component.index',
            'vendor.invoice.component.show',
            'vendor.invoice.component.store',
            'vendor.invoice.component.update',
            'vendor.invoice.component.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_invoice_component']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_invoice_component']);
        }
    }
}
