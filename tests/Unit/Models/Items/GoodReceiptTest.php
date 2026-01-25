<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\GoodReceipt;
use App\Models\Items\GoodReceiptComponent;
use App\Models\Purchases\PurchaseOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoodReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_order(): void
    {
        $order = PurchaseOrder::factory()->create();
        $receipt = GoodReceipt::factory()->create(['purchase_order_id' => $order->id]);

        $this->assertInstanceOf(PurchaseOrder::class, $receipt->order);
        $this->assertEquals($order->id, $receipt->order->id);
    }

    public function test_it_has_many_components(): void
    {
        $receipt = GoodReceipt::factory()->create();
        $component = GoodReceiptComponent::factory()->create(['good_receipt_id' => $receipt->id]);

        $this->assertTrue($receipt->components->contains($component));
    }
}
