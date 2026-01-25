<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_item(): void
    {
        $item = Item::factory()->create();
        $batch = ItemBatch::factory()->create(['item_id' => $item->id]);

        $this->assertInstanceOf(Item::class, $batch->item);
        $this->assertEquals($item->id, $batch->item->id);
    }

    public function test_it_has_one_stock(): void
    {
        $batch = ItemBatch::factory()->create();
        $stock = ItemStock::factory()->create(['item_batch_id' => $batch->id]);

        $this->assertInstanceOf(ItemStock::class, $batch->stock);
        $this->assertEquals($stock->id, $batch->stock->id);
    }
}
