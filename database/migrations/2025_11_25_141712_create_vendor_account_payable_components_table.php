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
		Schema::create('vendor_account_payable_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->ulid('vendor_account_payable_id');
			$table->foreign('vendor_account_payable_id', 'vendor_account_payable_foreign')->references('id')->on('vendor_account_payables')->onDelete('cascade');
			$table->ulid('purchase_invoice_component_id');
			$table->foreign('purchase_invoice_component_id', 'purchase_invoice_component_foreign')->references('id')->on('purchase_invoice_components')->onDelete('cascade');
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
		Schema::dropIfExists('vendor_account_payable_components');
	}
};
