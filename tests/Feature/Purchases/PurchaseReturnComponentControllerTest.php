<?php

namespace Tests\Feature\Purchases;

use App\Models\Items\GoodReceiptComponent;
use App\Models\Items\Item;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseReturn;
use App\Models\Purchases\PurchaseReturnComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PurchaseReturnComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_return_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());

        $return = PurchaseReturn::factory()->create();
        PurchaseReturnComponent::factory()->count(3)->create(['purchase_return_id' => $return->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.return.component.index', ['purchase_return_id' => $return->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'purchase_return_id', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.show')->where('guard_name', 'sanctum')->first());

        $component = PurchaseReturnComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.return.component.show', ['purchase_return_id' => $component->purchase_return_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    public function test_store_creates_purchase_return_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.return.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.store')->where('guard_name', 'sanctum')->first());

        $return = PurchaseReturn::factory()->create();
        $poComponent = PurchaseOrderComponent::factory()->create();
        $receiptComponent = GoodReceiptComponent::factory()->create();
        $item = Item::factory()->create();

        $array = [
            'purchase_return_id' => $return->id,
            'purchase_order_component_id' => $poComponent->id,
            'good_receipt_component_id' => $receiptComponent->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'price' => 100,
            'note' => 'Return item',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.return.component.store', ['purchase_return_id' => $return->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_return_components', ['purchase_return_id' => $return->id, 'total' => 500]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.return.index',
            'purchase.component.index',
            'purchase.component.show',
            'purchase.component.store',
            'purchase.component.update',
            'purchase.component.delete',
            'purchase.component.restore',
            'purchase.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_return_component']);
        }
    }
}
