<?php

namespace Tests\Unit\Models\Purchases;

use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_order(): void
    {
        $order = PurchaseOrder::factory()->create();
        $invoice = PurchaseInvoice::factory()->create(['purchase_order_id' => $order->id]);

        $this->assertInstanceOf(PurchaseOrder::class, $invoice->order);
        $this->assertEquals($order->id, $invoice->order->id);
    }

    public function test_it_has_many_components(): void
    {
        $invoice = PurchaseInvoice::factory()->create();
        $component = PurchaseInvoiceComponent::factory()->create(['purchase_invoice_id' => $invoice->id]);

        $this->assertTrue($invoice->components->contains($component));
    }
}
