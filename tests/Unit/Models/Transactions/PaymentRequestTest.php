<?php

namespace Tests\Unit\Models\Transactions;

use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Transactions\PaymentRequest;
use App\Models\Transactions\PaymentRequestComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_order(): void
    {
        $order = PurchaseOrder::factory()->create();
        $payment = PaymentRequest::factory()->create(['purchase_order_id' => $order->id]);

        $this->assertInstanceOf(PurchaseOrder::class, $payment->order);
        $this->assertEquals($order->id, $payment->order->id);
    }

    public function test_it_belongs_to_invoice(): void
    {
        $invoice = PurchaseInvoice::factory()->create();
        $payment = PaymentRequest::factory()->create(['purchase_invoice_id' => $invoice->id]);

        $this->assertInstanceOf(PurchaseInvoice::class, $payment->invoice);
        $this->assertEquals($invoice->id, $payment->invoice->id);
    }

    public function test_it_has_many_components(): void
    {
        $payment = PaymentRequest::factory()->create();
        $component = PaymentRequestComponent::factory()->create(['payment_request_id' => $payment->id]);

        $this->assertTrue($payment->components->contains($component));
    }
}
