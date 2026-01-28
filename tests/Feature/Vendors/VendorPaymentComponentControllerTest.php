<?php

namespace Tests\Feature\Vendors;

use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\VendorAccountPayableComponent;
use App\Models\Vendors\VendorPayment;
use App\Models\Vendors\VendorPaymentComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorPaymentComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_payment_components()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.index')->where('guard_name', 'sanctum')->first());

        $component = VendorPaymentComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.payment.component.index', ['vendor_payment_id' => $component->vendor_payment_id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_payment_id', 'vendor_account_payable_component_id', 'quantity', 'price', 'total'],
                ],
            ]);
    }

    public function test_show_returns_vendor_payment_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.show')->where('guard_name', 'sanctum')->first());

        $component = VendorPaymentComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.payment.component.show', ['vendor_payment_id' => $component->vendor_payment_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_vendor_payment_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.store')->where('guard_name', 'sanctum')->first());

        $array = VendorPaymentComponent::factory()->make()->toArray();
        $payment = VendorPayment::factory()->create();
        $accountPayableComponent = VendorAccountPayableComponent::factory()->create();

        $array['vendor_payment_id'] = $payment->id;
        $array['vendor_account_payable_component_id'] = $accountPayableComponent->id;

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.payment.component.store', ['vendor_payment_id' => $payment->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_payment_components', ['vendor_payment_id' => $array['vendor_payment_id'], 'quantity' => $array['quantity']]);
    }

    public function test_update_updates_vendor_payment_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.update')->where('guard_name', 'sanctum')->first());

        $component = VendorPaymentComponent::factory()->create();
        $newData = VendorPaymentComponent::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        $payment = VendorPayment::factory()->create();
        $accountPayableComponent = VendorAccountPayableComponent::factory()->create();
        $newData['vendor_payment_id'] = $payment->id;
        $newData['vendor_account_payable_component_id'] = $accountPayableComponent->id;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.payment.component.update', ['vendor_payment_id' => $component->vendor_payment_id, 'id' => $component->id]), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_payment_components', ['id' => $component->id, 'vendor_payment_id' => $newData['vendor_payment_id'], 'quantity' => $newData['quantity']]);
    }

    public function test_delete_soft_deletes_vendor_payment_component()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.component.delete')->where('guard_name', 'sanctum')->first());

        $component = VendorPaymentComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.payment.component.delete', ['vendor_payment_id' => $component->vendor_payment_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_payment_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.payment.index',
            'vendor.payment.component.index',
            'vendor.payment.component.show',
            'vendor.payment.component.store',
            'vendor.payment.component.update',
            'vendor.payment.component.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_payment_component']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_payment_component']);
        }
    }
}
