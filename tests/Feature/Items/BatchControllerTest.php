<?php

namespace Tests\Feature\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_batches()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.index')->where('guard_name', 'sanctum')->first());

        ItemBatch::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.batch.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'item_id', 'code', 'expired_at', 'is_available'],
                ],
            ]);
    }

    public function test_show_returns_batch()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.show')->where('guard_name', 'sanctum')->first());

        $batch = ItemBatch::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.batch.show', $batch->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $batch->id,
                    'code' => $batch->code,
                ],
            ]);
    }

    public function test_index_filters_by_item_id()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.index')->where('guard_name', 'sanctum')->first());

        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        ItemBatch::factory()->create(['item_id' => $item1->id]);
        ItemBatch::factory()->create(['item_id' => $item2->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.batch.index', ['item_id' => $item1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.item_id', $item1->id);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'item.index',
            'item.batch.index',
            'item.batch.show',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'item_batch']);
        }
    }
}
