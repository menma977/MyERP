<?php

namespace Tests\Feature\Items;

use App\Models\Items\GoodReceipt;
use App\Models\Purchases\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GoodReceiptControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_good_receipts(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());

        GoodReceipt::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.good.receipt.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'purchase_order_id', 'code', 'total'],
                ],
            ]);
    }

    public function test_show_returns_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.show')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.good.receipt.show', $goodReceipt->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $goodReceipt->id,
                'code' => $goodReceipt->code,
            ]);
    }

    public function test_update_updates_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.update')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();
        $newOrder = PurchaseOrder::factory()->create();

        $array = [
            'purchase_order_id' => $newOrder->id,
            'note' => 'Updated note',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.item.good.receipt.update', $goodReceipt->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('good_receipts', ['id' => $goodReceipt->id, 'purchase_order_id' => $newOrder->id, 'note' => 'Updated note']);
    }

    public function test_delete_soft_deletes_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.delete')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.good.receipt.delete', $goodReceipt->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('good_receipts', ['id' => $goodReceipt->id]);
    }

    public function test_restore_restores_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.restore')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();
        $goodReceipt->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.good.receipt.restore', $goodReceipt->id));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('good_receipts', ['id' => $goodReceipt->id]);
    }

    public function test_destroy_force_deletes_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.destroy')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();
        $goodReceipt->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.good.receipt.destroy', $goodReceipt->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('good_receipts', ['id' => $goodReceipt->id]);
    }

    public function test_approve_good_receipt(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.store')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.good.receipt.approve', $goodReceipt->id));

        $response->assertStatus(200);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'item.index',
            'good.receipt.index',
            'good.receipt.show',
            'good.receipt.update',
            'good.receipt.delete',
            'good.receipt.restore',
            'good.receipt.destroy',
            'good.receipt.store',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'good_receipt']);
        }
    }
}
