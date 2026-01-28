<?php

namespace Tests\Feature\Vendors;

use App\Enums\PaymentMethodEnum;
use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_payments()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());

        VendorPayment::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.payment.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_id', 'vendor_account_payable_id', 'amount', 'method'],
                ],
            ]);
    }

    public function test_show_returns_vendor_payment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.show')->where('guard_name', 'sanctum')->first());

        $payment = VendorPayment::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.payment.show', $payment->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $payment->id,
                ],
            ]);
    }

    public function test_store_creates_vendor_payment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.store')->where('guard_name', 'sanctum')->first());

        $array = VendorPayment::factory()->make()->toArray();
        $vendor = Vendor::factory()->create();
        $accountPayable = VendorAccountPayable::factory()->create();

        $array['vendor_id'] = $vendor->id;
        $array['vendor_account_payable_id'] = $accountPayable->id;
        $array['method'] = PaymentMethodEnum::BANK_TRANSFER->value;

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.payment.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_payments', ['vendor_id' => $array['vendor_id'], 'amount' => $array['amount']]);
    }

    public function test_update_updates_vendor_payment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.update')->where('guard_name', 'sanctum')->first());

        $payment = VendorPayment::factory()->create();
        $newData = VendorPayment::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        $vendor = Vendor::factory()->create();
        $accountPayable = VendorAccountPayable::factory()->create();
        $newData['vendor_id'] = $vendor->id;
        $newData['vendor_account_payable_id'] = $accountPayable->id;
        $newData['method'] = PaymentMethodEnum::BANK_TRANSFER->value;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.payment.update', $payment->id), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_payments', ['id' => $payment->id, 'vendor_id' => $newData['vendor_id'], 'amount' => $newData['amount']]);
    }

    public function test_delete_soft_deletes_vendor_payment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'vendor.payment.delete')->where('guard_name', 'sanctum')->first());

        $payment = VendorPayment::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.payment.delete', $payment->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_payments', ['id' => $payment->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.payment.index',
            'vendor.payment.show',
            'vendor.payment.store',
            'vendor.payment.update',
            'vendor.payment.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_payment']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_payment']);
        }
    }
}
