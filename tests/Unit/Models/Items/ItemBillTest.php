<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Item;
use App\Models\Items\ItemBill;
use App\Models\Items\ItemBillComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemBillTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_item(): void
    {
        $item = Item::factory()->create();
        $bill = ItemBill::factory()->create(['item_id' => $item->id]);

        $this->assertInstanceOf(Item::class, $bill->item);
        $this->assertEquals($item->id, $bill->item->id);
    }

    public function test_it_has_many_components(): void
    {
        $bill = ItemBill::factory()->create();
        $component = ItemBillComponent::factory()->create(['item_bill_id' => $bill->id]);

        $this->assertTrue($bill->component->contains($component));
    }
}
