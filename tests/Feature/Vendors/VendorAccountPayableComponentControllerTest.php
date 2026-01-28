<?php

namespace Tests\Feature\Vendors;

use App\Models\Permission;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\User;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorAccountPayableComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorAccountPayableComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_account_payable_components()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.index')->where('guard_name', 'sanctum')->first());

        $component = VendorAccountPayableComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.account.payable.component.index', ['vendor_account_payable_id' => $component->vendor_account_payable_id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_account_payable_id', 'purchase_invoice_component_id', 'quantity', 'price', 'total'],
                ],
            ]);
    }

    public function test_show_returns_vendor_account_payable_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.show')->where('guard_name', 'sanctum')->first());

        $component = VendorAccountPayableComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.account.payable.component.show', ['vendor_account_payable_id' => $component->vendor_account_payable_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_vendor_account_payable_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.store')->where('guard_name', 'sanctum')->first());

        $array = VendorAccountPayableComponent::factory()->make()->toArray();
        $accountPayable = VendorAccountPayable::factory()->create();
        $purchaseInvoiceComponent = PurchaseInvoiceComponent::factory()->create();

        $array['vendor_account_payable_id'] = $accountPayable->id;
        $array['purchase_invoice_component_id'] = $purchaseInvoiceComponent->id;

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.account.payable.component.store', ['vendor_account_payable_id' => $accountPayable->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_account_payable_components', ['vendor_account_payable_id' => $array['vendor_account_payable_id'], 'purchase_invoice_component_id' => $array['purchase_invoice_component_id']]);
    }

    public function test_update_updates_vendor_account_payable_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.update')->where('guard_name', 'sanctum')->first());

        $component = VendorAccountPayableComponent::factory()->create();
        $newData = VendorAccountPayableComponent::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        $accountPayable = VendorAccountPayable::factory()->create();
        $purchaseInvoiceComponent = PurchaseInvoiceComponent::factory()->create();
        $newData['vendor_account_payable_id'] = $accountPayable->id;
        $newData['purchase_invoice_component_id'] = $purchaseInvoiceComponent->id;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.account.payable.component.update', ['vendor_account_payable_id' => $component->vendor_account_payable_id, 'id' => $component->id]), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_account_payable_components', ['id' => $component->id, 'vendor_account_payable_id' => $newData['vendor_account_payable_id']]);
    }

    public function test_delete_soft_deletes_vendor_account_payable_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.account.payable.component.delete')->where('guard_name', 'sanctum')->first());

        $component = VendorAccountPayableComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.account.payable.component.delete', ['vendor_account_payable_id' => $component->vendor_account_payable_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_account_payable_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.account.payable.index',
            'vendor.account.payable.component.index',
            'vendor.account.payable.component.show',
            'vendor.account.payable.component.store',
            'vendor.account.payable.component.update',
            'vendor.account.payable.component.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_account_payable_component']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_account_payable_component']);
        }
    }
}
