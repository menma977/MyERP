<?php

namespace Tests\Feature\Vendors;

use App\Models\Permission;
use App\Models\User;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_vendor_invoices(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permInvIndex */
        $permInvIndex = Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvIndex);

        VendorInvoice::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.invoice.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vendor_id', 'code', 'total'],
                ],
            ]);
    }

    public function test_show_returns_vendor_invoice(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permInvIndex */
        $permInvIndex = Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvIndex);
        /** @var Permission $permInvShow */
        $permInvShow = Permission::where('name', 'vendor.invoice.show')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvShow);

        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.vendor.invoice.show', $vendorInvoice->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $vendorInvoice->id,
                    'code' => $vendorInvoice->code,
                ],
            ]);
    }

    public function test_store_creates_vendor_invoice(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permInvIndex */
        $permInvIndex = Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvIndex);
        /** @var Permission $permInvStore */
        $permInvStore = Permission::where('name', 'vendor.invoice.store')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvStore);

        $array = VendorInvoice::factory()->make()->toArray();
        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        $array['vendor_id'] = $vendor->id;
        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.vendor.invoice.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_invoices', ['code' => $array['code']]);
    }

    public function test_update_updates_vendor_invoice(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permInvIndex */
        $permInvIndex = Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvIndex);
        /** @var Permission $permInvUpdate */
        $permInvUpdate = Permission::where('name', 'vendor.invoice.update')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvUpdate);

        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::factory()->create();
        $newData = VendorInvoice::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

        /** @var Vendor $vendor */
        $vendor = Vendor::factory()->create();
        $newData['vendor_id'] = $vendor->id;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.vendor.invoice.update', $vendorInvoice->id), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vendor_invoices', ['id' => $vendorInvoice->id, 'code' => $newData['code']]);
    }

    public function test_delete_soft_deletes_vendor_invoice(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'vendor.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permInvIndex */
        $permInvIndex = Permission::where('name', 'vendor.invoice.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvIndex);
        /** @var Permission $permInvDelete */
        $permInvDelete = Permission::where('name', 'vendor.invoice.delete')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permInvDelete);

        /** @var VendorInvoice $vendorInvoice */
        $vendorInvoice = VendorInvoice::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.vendor.invoice.delete', $vendorInvoice->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('vendor_invoices', ['id' => $vendorInvoice->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'vendor.index',
            'vendor.invoice.index',
            'vendor.invoice.show',
            'vendor.invoice.store',
            'vendor.invoice.update',
            'vendor.invoice.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'vendor_invoice']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'vendor_invoice']);
        }
    }
}
