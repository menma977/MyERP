<?php

namespace Tests\Unit\Models\Vendors;

use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorAccountPayableComponent;
use App\Models\Vendors\VendorInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorAccountPayableTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_vendor(): void
    {
        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create();
        $this->assertInstanceOf(Vendor::class, $accountPayable->vendor);
    }

    public function test_it_belongs_to_vendor_invoice(): void
    {
        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create();
        $this->assertInstanceOf(VendorInvoice::class, $accountPayable->vendorInvoice);
    }

    public function test_it_has_many_components(): void
    {
        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create();
        VendorAccountPayableComponent::factory()->count(3)->create(['vendor_account_payable_id' => $accountPayable->id]);

        $this->assertCount(3, $accountPayable->components);
        $this->assertInstanceOf(VendorAccountPayableComponent::class, $accountPayable->components->first());
    }

    public function test_casts_attributes_correctly(): void
    {
        /** @var VendorAccountPayable $accountPayable */
        $accountPayable = VendorAccountPayable::factory()->create([
            'amount' => '100.50',
        ]);

        $this->assertSame('100.50', $accountPayable->amount);
    }
}
