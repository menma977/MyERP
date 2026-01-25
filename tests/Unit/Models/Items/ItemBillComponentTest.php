<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBill;
use App\Models\Items\ItemBillComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemBillComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_bill(): void
    {
        $bill = ItemBill::factory()->create();
        $component = ItemBillComponent::factory()->create(['item_bill_id' => $bill->id]);

        $this->assertInstanceOf(ItemBill::class, $component->bill);
        $this->assertEquals($bill->id, $component->bill->id);
    }

    public function test_it_belongs_to_item(): void
    {
        $item = Item::factory()->create();
        $component = ItemBillComponent::factory()->create(['item_id' => $item->id]);

        $this->assertInstanceOf(Item::class, $component->item);
        $this->assertEquals($item->id, $component->item->id);
    }
}
