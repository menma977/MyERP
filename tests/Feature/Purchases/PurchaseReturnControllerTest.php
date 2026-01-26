<?php

namespace Tests\Feature\Purchases;

use App\Models\Items\GoodReceipt;
use App\Models\Permission;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseReturnControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_returns(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());

        PurchaseReturn::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.return.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.return.show')->where('guard_name', 'sanctum')->first());

        $purchaseReturn = PurchaseReturn::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.return.show', $purchaseReturn->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $purchaseReturn->id,
                    'code' => $purchaseReturn->code,
                ],
            ]);
    }

    public function test_store_creates_purchase_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.return.store')->where('guard_name', 'sanctum')->first());

        $order = PurchaseOrder::factory()->create();
        $receipt = GoodReceipt::factory()->create();

        $array = [
            'purchase_order_id' => $order->id,
            'good_receipt_id' => $receipt->id,
            'note' => 'New return',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.return.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_returns', ['purchase_order_id' => $order->id, 'good_receipt_id' => $receipt->id]);
    }

    public function test_update_updates_purchase_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.return.update')->where('guard_name', 'sanctum')->first());

        $purchaseReturn = PurchaseReturn::factory()->create();
        $newOrder = PurchaseOrder::factory()->create();
        $newReceipt = GoodReceipt::factory()->create();

        $array = [
            'purchase_order_id' => $newOrder->id,
            'good_receipt_id' => $newReceipt->id,
            'note' => 'Updated note',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.purchase.return.update', $purchaseReturn->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_returns', ['id' => $purchaseReturn->id, 'note' => 'Updated note']);
    }

    public function test_delete_soft_deletes_purchase_return(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.return.delete')->where('guard_name', 'sanctum')->first());

        $purchaseReturn = PurchaseReturn::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.purchase.return.delete', $purchaseReturn->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('purchase_returns', ['id' => $purchaseReturn->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.return.index',
            'purchase.return.show',
            'purchase.return.store',
            'purchase.return.update',
            'purchase.return.delete',
            'purchase.return.restore',
            'purchase.return.destroy',
            'purchase.return.approve',
            'purchase.return.reject',
            'purchase.return.cancel',
            'purchase.return.rollback',
            'purchase.return.force',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_return']);
        }
    }
}
