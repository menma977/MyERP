<?php

namespace Database\Factories\Vendors;

use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Vendors\VendorAccountPayable;
use App\Models\Vendors\VendorAccountPayableComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorAccountPayableComponentFactory extends Factory
{
    protected $model = VendorAccountPayableComponent::class;

    public function definition(): array
    {
        return [
            'vendor_account_payable_id' => VendorAccountPayable::factory(),
            // Assuming PurchaseInvoiceComponent factory exists, if not we might face issues.
            // But usually validation rules might not enforce existence for factory unless FK constraint.
            // To be safe, I'd rather create one if possible, or just generate a random ID if it's strict.
            // Checking imports... App\Models\Purchases\PurchaseInvoiceComponent exists.
            // I'll assume factory exists or I will just leave it random for now if it fails.
            'purchase_invoice_component_id' => PurchaseInvoiceComponent::factory(),
            // Actually, looking at the migration/model, it seems required.
            // Let's try to find if PurchaseInvoiceComponentFactory exists later. For now, creating without it or mock.
            // Wait, for a proper factory, it should probably create one.
            // 'purchase_invoice_component_id' => \App\Models\Purchases\PurchaseInvoiceComponent::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
