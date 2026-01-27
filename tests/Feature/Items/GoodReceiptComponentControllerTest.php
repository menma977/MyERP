<?php

namespace Tests\Feature\Items;

use App\Models\Items\GoodReceipt;
use App\Models\Items\GoodReceiptComponent;
use App\Models\Items\Item;
use App\Models\Permission;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GoodReceiptComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_good_receipt_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();
        GoodReceiptComponent::factory()->count(3)->create(['good_receipt_id' => $goodReceipt->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.good.receipt.component.index', ['good_receipt_id' => $goodReceipt->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'good_receipt_id', 'item_id', 'quantity'],
                ],
            ]);
    }

    public function test_show_returns_good_receipt_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.show')->where('guard_name', 'sanctum')->first());

        $component = GoodReceiptComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.good.receipt.component.show', ['good_receipt_id' => $component->good_receipt_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_good_receipt_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.store')->where('guard_name', 'sanctum')->first());

        $goodReceipt = GoodReceipt::factory()->create();
        $item = Item::factory()->create();
        $poComponent = PurchaseOrderComponent::factory()->create();

        $array = [
            'good_receipt_id' => $goodReceipt->id,
            'purchase_order_component_id' => $poComponent->id,
            'item_id' => $item->id,
            'quantity' => 10,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.good.receipt.component.store', ['good_receipt_id' => $goodReceipt->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('good_receipt_components', ['good_receipt_id' => $goodReceipt->id, 'item_id' => $item->id, 'quantity' => 10]);
    }

    public function test_update_updates_good_receipt_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.update')->where('guard_name', 'sanctum')->first());

        $component = GoodReceiptComponent::factory()->create();
        $newData = [
            'good_receipt_id' => $component->good_receipt_id,
            'purchase_order_component_id' => $component->purchase_order_component_id,
            'item_id' => $component->item_id,
            'quantity' => 50,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.item.good.receipt.component.update', ['good_receipt_id' => $component->good_receipt_id, 'id' => $component->id]), $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('good_receipt_components', ['id' => $component->id, 'quantity' => 50]);
    }

    public function test_delete_soft_deletes_good_receipt_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.delete')->where('guard_name', 'sanctum')->first());

        $component = GoodReceiptComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.good.receipt.component.delete', ['good_receipt_id' => $component->good_receipt_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('good_receipt_components', ['id' => $component->id]);
    }

    public function test_restore_restores_good_receipt_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.restore')->where('guard_name', 'sanctum')->first());

        $component = GoodReceiptComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.item.good.receipt.component.restore', ['good_receipt_id' => $component->good_receipt_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('good_receipt_components', ['id' => $component->id]);
    }

    public function test_destroy_force_deletes_good_receipt_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'good.receipt.component.destroy')->where('guard_name', 'sanctum')->first());

        $component = GoodReceiptComponent::factory()->create();
        $component->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.item.good.receipt.component.destroy', ['good_receipt_id' => $component->good_receipt_id, 'id' => $component->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('good_receipt_components', ['id' => $component->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'item.index',
            'good.receipt.index',
            'good.receipt.component.index',
            'good.receipt.component.show',
            'good.receipt.component.store',
            'good.receipt.component.update',
            'good.receipt.component.delete',
            'good.receipt.component.restore',
            'good.receipt.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'good_receipt_component']);
        }
    }
}
