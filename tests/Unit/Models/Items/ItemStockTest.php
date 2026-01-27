<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\ItemBatch;
use App\Models\Items\ItemStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_batch(): void
    {
        $batch = ItemBatch::factory()->create();
        $stock = ItemStock::factory()->create(['item_batch_id' => $batch->id]);

        $this->assertInstanceOf(ItemBatch::class, $stock->batch);
        $this->assertEquals($batch->id, $stock->batch->id);
    }
}
