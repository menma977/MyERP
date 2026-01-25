<?php

namespace Tests\Unit\Models\Purchases;

use App\Models\Purchases\PurchaseProcurement;
use App\Models\Purchases\PurchaseProcurementComponent;
use App\Models\Purchases\PurchaseRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseProcurementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_request(): void
    {
        $request = PurchaseRequest::factory()->create();
        $procurement = PurchaseProcurement::factory()->create(['purchase_request_id' => $request->id]);

        $this->assertInstanceOf(PurchaseRequest::class, $procurement->request);
        $this->assertEquals($request->id, $procurement->request->id);
    }

    public function test_it_has_many_components(): void
    {
        $procurement = PurchaseProcurement::factory()->create();
        $component = PurchaseProcurementComponent::factory()->create(['purchase_procurement_id' => $procurement->id]);

        $this->assertTrue($procurement->components->contains($component));
    }
}
