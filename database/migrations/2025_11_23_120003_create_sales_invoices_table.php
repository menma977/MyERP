<?php

use App\Enums\DiscountTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->ulid('id')->primary()->index();
            $table->foreignUlid('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('total', 18, 4)->default(0);
            $table->decimal('tax', 18, 4)->default(0);
            $table->string('discount_type')->default(DiscountTypeEnum::PERCENT);
            $table->decimal('discount', 18, 4)->default(0);
            $table->decimal('fee', 18, 4)->default(0);
            $table->decimal('grand_total', 18, 4)->default(0);
            $table->decimal('paid', 18, 4)->default(0);
            $table->text('note')->nullable();
            $table->string('method')->default(PaymentMethodEnum::CASH->value)->index();
            $table->string('status')->default(PaymentStatusEnum::PAID->value)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
