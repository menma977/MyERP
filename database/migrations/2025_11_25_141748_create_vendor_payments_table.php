<?php

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('vendor_payments', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
			$table->foreignUlid('vendor_account_payable_id')->constrained('vendor_account_payables')->cascadeOnDelete();
			$table->decimal('amount', 18, 4)->default(0);
			$table->string('method')->default(PaymentMethodEnum::CASH->value);
			$table->text('note')->nullable();
			$table->timestamp('paid_at')->nullable();
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
		Schema::dropIfExists('vendor_payments');
	}
};
