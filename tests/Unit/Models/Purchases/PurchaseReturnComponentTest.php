<?php

namespace Tests\Unit\Models\Purchases;

use App\Models\Items\GoodReceiptComponent;
use App\Models\Items\Item;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseReturn;
use App\Models\Purchases\PurchaseReturnComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseReturnComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_return(): void
    {
        $return = PurchaseReturn::factory()->create();
        $component = PurchaseReturnComponent::factory()->create(['purchase_return_id' => $return->id]);

        $this->assertInstanceOf(PurchaseReturn::class, $component->return);
        $this->assertEquals($return->id, $component->return->id);
    }

    public function test_it_belongs_to_order_component(): void
    {
        $orderComponent = PurchaseOrderComponent::factory()->create();
        $component = PurchaseReturnComponent::factory()->create(['purchase_order_component_id' => $orderComponent->id]);

        $this->assertInstanceOf(PurchaseOrderComponent::class, $component->orderComponent);
        $this->assertEquals($orderComponent->id, $component->orderComponent->id);
    }

    public function test_it_belongs_to_good_receipt_component(): void
    {
        $receiptComponent = GoodReceiptComponent::factory()->create();
        $component = PurchaseReturnComponent::factory()->create(['good_receipt_component_id' => $receiptComponent->id]);

        $this->assertInstanceOf(GoodReceiptComponent::class, $component->goodReceiptComponent);
        $this->assertEquals($receiptComponent->id, $component->goodReceiptComponent->id);
    }

    public function test_it_belongs_to_item(): void
    {
        $item = Item::factory()->create();
        $component = PurchaseReturnComponent::factory()->create(['item_id' => $item->id]);

        $this->assertInstanceOf(Item::class, $component->item);
        $this->assertEquals($item->id, $component->item->id);
    }
}
