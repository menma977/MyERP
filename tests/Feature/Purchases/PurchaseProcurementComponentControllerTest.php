<?php

namespace Tests\Feature\Purchases;

use App\Models\Items\Item;
use App\Models\Permission;
use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseProcurementComponent;
use App\Models\User;
use App\Models\Vendors\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseProcurementComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_procurement_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());

        $procurement = PurchaseProcurement::factory()->create();
        PurchaseProcurementComponent::factory()->count(3)->create(['purchase_procurement_id' => $procurement->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.procurement.component.index', ['purchase_procurement_id' => $procurement->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'purchase_procurement_id', 'note'],
                ],
            ]);
    }

    public function test_show_returns_purchase_procurement_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.show')->where('guard_name', 'sanctum')->first());

        $component = PurchaseProcurementComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.procurement.component.show', ['purchase_procurement_id' => $component->purchase_procurement_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $component->id,
                ],
            ]);
    }

    public function test_store_creates_purchase_procurement_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.store')->where('guard_name', 'sanctum')->first());

        $procurement = PurchaseProcurement::factory()->create();
        $item = Item::factory()->create();
        $vendor = Vendor::factory()->create();

        $array = [
            'purchase_procurement_id' => $procurement->id,
            'item_id' => $item->id,
            'vendor_id' => $vendor->id,
            'price' => 100,
            'quantity' => 10,
            'note' => 'New procurement component note',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.procurement.component.store', ['purchase_procurement_id' => $procurement->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_procurement_components', ['purchase_procurement_id' => $procurement->id, 'note' => 'New procurement component note', 'total' => 1000]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.procurement.index',
            'purchase.component.index',
            'purchase.component.show',
            'purchase.component.store',
            'purchase.component.update',
            'purchase.component.delete',
            'purchase.component.restore',
            'purchase.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_procurement_component']);
        }
    }
}
