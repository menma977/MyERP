<?php

namespace Tests\Feature\Purchases;

use App\Models\Permission;
use App\Models\Purchases\PurchaseRequest;
use App\Models\Purchases\PurchaseRequestComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_requests(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());

        PurchaseRequest::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.request.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total'],
                ],
            ]);
    }

    public function test_show_returns_purchase_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.request.show')->where('guard_name', 'sanctum')->first());

        $purchaseRequest = PurchaseRequest::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.request.show', $purchaseRequest->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $purchaseRequest->id,
                    'code' => $purchaseRequest->code,
                ],
            ]);
    }

    public function test_store_creates_purchase_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.request.store')->where('guard_name', 'sanctum')->first());

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.request.store'));

        $response->assertStatus(200);
        $this->assertDatabaseCount('purchase_requests', 1);
    }

    public function test_delete_soft_deletes_purchase_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.request.delete')->where('guard_name', 'sanctum')->first());

        $purchaseRequest = PurchaseRequest::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.purchase.request.delete', $purchaseRequest->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('purchase_requests', ['id' => $purchaseRequest->id]);
    }

    public function test_approve_creates_purchase_procurement(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.request.store')->where('guard_name', 'sanctum')->first());

        $purchaseRequest = PurchaseRequest::factory()->create();
        PurchaseRequestComponent::factory()->count(2)->create(['purchase_request_id' => $purchaseRequest->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.purchase.request.approve', $purchaseRequest->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_procurements', ['purchase_request_id' => $purchaseRequest->id]);
        $this->assertDatabaseCount('purchase_procurement_components', 2);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.request.index',
            'purchase.request.show',
            'purchase.request.store',
            'purchase.request.delete',
            'purchase.request.restore',
            'purchase.request.destroy',
            'purchase.request.reject',
            'purchase.request.cancel',
            'purchase.request.rollback',
            'purchase.request.force',
            'purchase.procurement.store',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_request']);
        }
    }
}
