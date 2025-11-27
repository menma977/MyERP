<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @noinspection DuplicatedCode
	 */
	public function up(): void
	{
		Schema::create('vendor_payment_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('vendor_payment_id')->constrained('vendor_payments')->cascadeOnDelete();
			$table->ulid('vendor_account_payable_component_id');
			$table->foreign('vendor_account_payable_component_id', 'vendor_account_payable_component_foreign')->references('id')->on('vendor_account_payable_components')->onDelete('cascade');
			$table->decimal('quantity', 18, 4)->default(0);
			$table->decimal('price', 18, 4)->default(0);
			$table->decimal('total', 18, 4)->default(0);
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
		Schema::dropIfExists('vendor_payment_components');
	}
};
