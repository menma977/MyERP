<?php

namespace Tests\Feature\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use App\Models\Items\ItemStockHistory;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_histories(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.history.index')->where('guard_name', 'sanctum')->first());

        ItemStockHistory::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.stock.history.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'item_stock_id', 'code', 'quantity', 'price'],
                ],
            ]);
    }

    public function test_show_returns_history(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.history.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.history.show')->where('guard_name', 'sanctum')->first());

        $history = ItemStockHistory::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.stock.history.show', $history->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $history->id,
                ],
            ]);
    }

    public function test_index_filters_by_item_id(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'item.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'item.batch.stock.history.index')->where('guard_name', 'sanctum')->first());

        $item1 = Item::factory()->create();
        $batch1 = ItemBatch::factory()->create(['item_id' => $item1->id]);
        $stock1 = ItemStock::factory()->create(['item_batch_id' => $batch1->id]);
        $history1 = ItemStockHistory::factory()->create(['item_stock_id' => $stock1->id]);

        $item2 = Item::factory()->create();
        $batch2 = ItemBatch::factory()->create(['item_id' => $item2->id]);
        $stock2 = ItemStock::factory()->create(['item_batch_id' => $batch2->id]);
        $history2 = ItemStockHistory::factory()->create(['item_stock_id' => $stock2->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.item.stock.history.index', ['item_id' => $item1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $history1->id);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'item.index',
            'item.batch.index',
            'item.batch.stock.index',
            'item.batch.stock.history.index',
            'item.batch.stock.history.show',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'item_stock_history']);
        }
    }
}
