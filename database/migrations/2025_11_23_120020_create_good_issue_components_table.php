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
		Schema::create('good_issue_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('good_issue_id')->constrained('good_issues')->cascadeOnDelete();
			$table->foreignUlid('sales_invoice_component_id')->constrained('sales_invoice_components')->cascadeOnDelete();
			$table->foreignUlid('item_id')->constrained('items')->cascadeOnDelete();
			$table->foreignUlid('item_batch_id')->constrained('item_batches')->cascadeOnDelete();
			$table->foreignUlid('item_stock_id')->constrained('item_stocks')->cascadeOnDelete();
			$table->decimal('quantity', 18, 4)->default(0);
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
		Schema::dropIfExists('good_issue_components');
	}
};
