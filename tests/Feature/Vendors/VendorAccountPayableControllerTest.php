<?php

namespace Tests\Feature\Vendors;

use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorAccountPayableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_account_payables(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permAccIndex */
        $permAccIndex = Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccIndex);

        VendorAccountPayable::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.account.payable.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_id', 'vendor_invoice_id', 'amount', 'note'],
                ],
            ]);
    }

    public function test_show_returns_vendor_account_payable(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permAccIndex */
        $permAccIndex = Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccIndex);
        /** @var Permission $permAccShow */
        $permAccShow = Permission::where('name', 'vendor.account.payable.show')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccShow);

        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.account.payable.show', $accountPayable->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $accountPayable->id,
                ],
            ]);
    }

    public function test_store_creates_vendor_account_payable(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permAccIndex */
        $permAccIndex = Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccIndex);
        /** @var Permission $permAccStore */
        $permAccStore = Permission::where('name', 'vendor.account.payable.store')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccStore);

        $array = VendorAccountPayable::factory()->make()->toArray();
        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        /** @var VendorInvoice $invoice */
        $invoice = VendorInvoice::factory()->create();

        $array['vendor_id'] = $vendor->id;
        $array['vendor_invoice_id'] = $invoice->id;

        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.account.payable.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_account_payables', ['vendor_id' => $array['vendor_id'], 'amount' => $array['amount']]);
    }

    public function test_update_updates_vendor_account_payable(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permAccIndex */
        $permAccIndex = Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccIndex);
        /** @var Permission $permAccUpdate */
        $permAccUpdate = Permission::where('name', 'vendor.account.payable.update')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccUpdate);

        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create();
        $newData = VendorAccountPayable::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        /** @var VendorInvoice $invoice */
        $invoice = VendorInvoice::factory()->create();
        $newData['vendor_id'] = $vendor->id;
        $newData['vendor_invoice_id'] = $invoice->id;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.account.payable.update', $accountPayable->id), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_account_payables', ['id' => $accountPayable->id, 'vendor_id' => $newData['vendor_id'], 'amount' => $newData['amount']]);
    }

    public function test_delete_soft_deletes_vendor_account_payable(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permAccIndex */
        $permAccIndex = Permission::where('name', 'vendor.account.payable.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccIndex);
        /** @var Permission $permAccDelete */
        $permAccDelete = Permission::where('name', 'vendor.account.payable.delete')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permAccDelete);

        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.account.payable.delete', $accountPayable->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_account_payables', ['id' => $accountPayable->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.account.payable.index',
            'vendor.account.payable.show',
            'vendor.account.payable.store',
            'vendor.account.payable.update',
            'vendor.account.payable.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_account_payable']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_account_payable']);
        }
    }
}
