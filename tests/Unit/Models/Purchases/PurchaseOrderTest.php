<?php

namespace Tests\Unit\Models\Purchases;

use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_request(): void
    {
        $request = PurchaseRequest::factory()->create();
        $order = PurchaseOrder::factory()->create(['purchase_request_id' => $request->id]);

        $this->assertInstanceOf(PurchaseRequest::class, $order->request);
        $this->assertEquals($request->id, $order->request->id);
    }

    public function test_it_belongs_to_procurement(): void
    {
        $procurement = PurchaseProcurement::factory()->create();
        $order = PurchaseOrder::factory()->create(['purchase_procurement_id' => $procurement->id]);

        $this->assertInstanceOf(PurchaseProcurement::class, $order->procurement);
        $this->assertEquals($procurement->id, $order->procurement->id);
    }

    public function test_it_has_many_components(): void
    {
        $order = PurchaseOrder::factory()->create();
        $component = PurchaseOrderComponent::factory()->create(['purchase_order_id' => $order->id]);

        $this->assertTrue($order->components->contains($component));
    }
}
