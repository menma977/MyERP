<?php

namespace Tests\Unit\Models\Purchases;

use App\Models\Purchases\PurchaseRequest;
use App\Models\Purchases\PurchaseRequestComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_components(): void
    {
        $request = PurchaseRequest::factory()->create();
        $component = PurchaseRequestComponent::factory()->create(['purchase_request_id' => $request->id]);

        $this->assertTrue($request->components->contains($component));
    }
}
