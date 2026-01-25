<?php

namespace Tests\Unit\Models\Purchases;

use App\Models\Items\GoodReceipt;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseReturn;
use App\Models\Purchases\PurchaseReturnComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_order(): void
    {
        $order = PurchaseOrder::factory()->create();
        $return = PurchaseReturn::factory()->create(['purchase_order_id' => $order->id]);

        $this->assertInstanceOf(PurchaseOrder::class, $return->order);
        $this->assertEquals($order->id, $return->order->id);
    }

    public function test_it_belongs_to_good_receipt(): void
    {
        $receipt = GoodReceipt::factory()->create();
        $return = PurchaseReturn::factory()->create(['good_receipt_id' => $receipt->id]);

        $this->assertInstanceOf(GoodReceipt::class, $return->goodReceipt);
        $this->assertEquals($receipt->id, $return->goodReceipt->id);
    }

    public function test_it_has_many_components(): void
    {
        $return = PurchaseReturn::factory()->create();
        $component = PurchaseReturnComponent::factory()->create(['purchase_return_id' => $return->id]);

        $this->assertTrue($return->components->contains($component));
    }
}
