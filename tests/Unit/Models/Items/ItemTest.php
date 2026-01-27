<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_batches(): void
    {
        $item = Item::factory()->create();
        $batch = ItemBatch::factory()->create(['item_id' => $item->id]);

        $this->assertTrue($item->batches->contains($batch));
        $this->assertInstanceOf(ItemBatch::class, $item->batches->first());
    }

    public function test_it_casts_attributes(): void
    {
        $item = Item::factory()->create([
            'cost' => '123.45',
        ]);

        $this->assertEquals('123.45', $item->cost);
    }
}
