<?php

namespace Tests\Feature\Purchases;

use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PurchaseProcurementControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_purchase_procurements(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.index')->where('guard_name', 'sanctum')->first());

        PurchaseProcurement::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.procurement.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'purchase_request_id'],
                ],
            ]);
    }

    public function test_show_returns_purchase_procurement(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.show')->where('guard_name', 'sanctum')->first());

        $procurement = PurchaseProcurement::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.purchase.procurement.show', $procurement->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $procurement->id,
                'code' => $procurement->code,
            ]);
    }

    public function test_update_updates_purchase_procurement(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'purchase.procurement.update')->where('guard_name', 'sanctum')->first());

        $procurement = PurchaseProcurement::factory()->create();
        $newRequest = PurchaseRequest::factory()->create();
        $array = [
            'purchase_request_id' => $newRequest->id,
            'note' => 'Updated procurement note',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.purchase.procurement.update', $procurement->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('purchase_procurements', ['id' => $procurement->id, 'note' => 'Updated procurement note']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'purchase.procurement.index',
            'purchase.procurement.show',
            'purchase.procurement.update',
            'purchase.procurement.restore',
            'purchase.procurement.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'purchase_procurement']);
        }
    }
}
