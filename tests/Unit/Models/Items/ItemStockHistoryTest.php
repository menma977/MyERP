<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\ItemStock;
use App\Models\Items\ItemStockHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemStockHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_stock(): void
    {
        $stock = ItemStock::factory()->create();
        $history = ItemStockHistory::factory()->create(['item_stock_id' => $stock->id]);

        $this->assertInstanceOf(ItemStock::class, $history->stock);
        $this->assertEquals($stock->id, $history->stock->id);
    }
}
