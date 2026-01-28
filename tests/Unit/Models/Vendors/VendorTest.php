<?php

namespace Tests\Unit\Models\Vendors;

use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_components(): void
    {
        $vendor = Vendor::factory()->create();
        $component = VendorComponent::factory()->create(['vendor_id' => $vendor->id]);

        $this->assertTrue($vendor->components->contains($component));
        $this->assertInstanceOf(VendorComponent::class, $vendor->components->first());
    }

    public function test_it_casts_attributes(): void
    {
        $vendor = Vendor::factory()->create();

        $this->assertNotNull($vendor->created_at);
        $this->assertIsString($vendor->ulid);
    }
}
