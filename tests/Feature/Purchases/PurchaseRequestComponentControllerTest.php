<?php

namespace Tests\Feature\Purchases;

use App\Models\Items\Item;
use App\Models\Purchases\PurchaseRequest;
use App\Models\Purchases\PurchaseRequestComponent;
use App\Models\User;
use App\Models\Vendors\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PurchaseRequestComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_request_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());

        $request = PurchaseRequest::factory()->create();
        PurchaseRequestComponent::factory()->count(3)->create(['purchase_request_id' => $request->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.request.component.index', ['purchase_request_id' => $request->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'purchase_request_id', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_request_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.show')->where('guard_name', 'sanctum')->first());

        $component = PurchaseRequestComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.request.component.show', ['purchase_request_id' => $component->purchase_request_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    public function test_store_creates_purchase_request_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.component.store')->where('guard_name', 'sanctum')->first());

        $request = PurchaseRequest::factory()->create();
        $vendor = Vendor::factory()->create();
        $item = Item::factory()->create();

        $array = [
            'purchase_request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'item_id' => $item->id,
            'price' => 100,
            'quantity' => 10,
            'note' => 'New component',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.request.component.store', ['purchase_request_id' => $request->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_request_components', ['purchase_request_id' => $request->id, 'total' => 1000]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.request.index',
            'purchase.component.index',
            'purchase.component.show',
            'purchase.component.store',
            'purchase.component.update',
            'purchase.component.delete',
            'purchase.component.restore',
            'purchase.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_request_component']);
        }
    }
}
