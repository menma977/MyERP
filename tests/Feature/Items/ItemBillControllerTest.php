<?php

namespace Tests\Feature\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBill;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemBillControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_item_bills(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());

        ItemBill::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.bill.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'item_id', 'code', 'quantity'],
                ],
            ]);
    }

    public function test_show_returns_item_bill(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.show')->where('guard_name', 'sanctum')->first());

        $itemBill = ItemBill::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.bill.show', $itemBill->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $itemBill->id,
                    'code' => $itemBill->code,
                ],
            ]);
    }

    public function test_store_creates_item_bill(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.store')->where('guard_name', 'sanctum')->first());

        $item = Item::factory()->create();
        $componentItem = Item::factory()->create();

        $array = [
            'item_id' => $item->id,
            'code' => 'BILL-NEW-001',
            'quantity' => 10,
            'components' => [
                [
                    'item_id' => $componentItem->id,
                    'quantity' => 5,
                ],
            ],
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.bill.store'), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('item_bills', ['code' => 'BILL-NEW-001']);
        $this->assertDatabaseHas('item_bill_components', ['item_id' => $componentItem->id, 'quantity' => 5]);
    }

    public function test_update_updates_item_bill(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.update')->where('guard_name', 'sanctum')->first());

        $itemBill = ItemBill::factory()->create();
        $componentItem = Item::factory()->create();

        $array = [
            'item_id' => $itemBill->item_id,
            'code' => 'BILL-UPDATED',
            'quantity' => 20,
            'components' => [
                [
                    'item_id' => $componentItem->id,
                    'quantity' => 15,
                ],
            ],
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.item.bill.update', $itemBill->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('item_bills', ['id' => $itemBill->id, 'code' => 'BILL-UPDATED']);
    }

    public function test_delete_soft_deletes_item_bill(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.delete')->where('guard_name', 'sanctum')->first());

        $itemBill = ItemBill::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.bill.delete', $itemBill->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('item_bills', ['id' => $itemBill->id]);
    }

    public function test_restore_restores_item_bill(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.restore')->where('guard_name', 'sanctum')->first());

        $itemBill = ItemBill::factory()->create();
        $itemBill->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.bill.restore', $itemBill->id));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('item_bills', ['id' => $itemBill->id]);
    }

    public function test_destroy_force_deletes_item_bill(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.bill.destroy')->where('guard_name', 'sanctum')->first());

        $itemBill = ItemBill::factory()->create();
        $itemBill->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.bill.destroy', $itemBill->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('item_bills', ['id' => $itemBill->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'item.index',
            'item.bill.index',
            'item.bill.show',
            'item.bill.store',
            'item.bill.update',
            'item.bill.delete',
            'item.bill.restore',
            'item.bill.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'item_bill']);
        }
    }
}
