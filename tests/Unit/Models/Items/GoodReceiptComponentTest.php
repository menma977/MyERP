<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\GoodReceipt;
use App\Models\Items\GoodReceiptComponent;
use App\Models\Items\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoodReceiptComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_receipt(): void
    {
        $receipt = GoodReceipt::factory()->create();
        $component = GoodReceiptComponent::factory()->create(['good_receipt_id' => $receipt->id]);

        $this->assertInstanceOf(GoodReceipt::class, $component->goodReceipt);
        $this->assertEquals($receipt->id, $component->goodReceipt->id);
    }

    public function test_it_belongs_to_item(): void
    {
        $item = Item::factory()->create();
        $component = GoodReceiptComponent::factory()->create(['item_id' => $item->id]);

        $this->assertInstanceOf(Item::class, $component->item);
        $this->assertEquals($item->id, $component->item->id);
    }
}
