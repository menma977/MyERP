<?php

namespace Tests\Unit\Models\Vendors;

use App\Enums\PaymentMethodEnum;
use App\Models\Vendors\Vendor;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorPayment;
use App\Models\Vendors\VendorPaymentComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_vendor()
    {
        $payment = VendorPayment::factory()->create();
        $this->assertInstanceOf(Vendor::class, $payment->vendor);
    }

    public function test_it_belongs_to_account_payable()
    {
        $payment = VendorPayment::factory()->create();
        $this->assertInstanceOf(VendorAccountPayable::class, $payment->accountPayable);
    }

    public function test_it_has_many_components()
    {
        $payment = VendorPayment::factory()->create();
        VendorPaymentComponent::factory()->count(3)->create(['vendor_payment_id' => $payment->id]);

        $this->assertCount(3, $payment->components);
        $this->assertInstanceOf(VendorPaymentComponent::class, $payment->components->first());
    }

    public function test_casts_attributes_correctly()
    {
        $payment = VendorPayment::factory()->create([
            'method' => PaymentMethodEnum::BANK_TRANSFER,
            'amount' => '500.00',
        ]);

        $this->assertInstanceOf(PaymentMethodEnum::class, $payment->method);
        $this->assertEquals(PaymentMethodEnum::BANK_TRANSFER, $payment->method);
        $this->assertSame('500.00', $payment->amount);
    }
}
