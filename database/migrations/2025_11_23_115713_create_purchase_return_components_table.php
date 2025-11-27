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
		Schema::create('purchase_return_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('purchase_return_id')->constrained('purchase_returns')->cascadeOnDelete();
			$table->foreignUlid('purchase_order_component_id')->constrained('purchase_order_components')->cascadeOnDelete();
			$table->foreignUlid('good_receipt_component_id')->constrained('good_receipt_components')->cascadeOnDelete();
			$table->foreignUlid('item_id')->constrained('items')->cascadeOnDelete();
			$table->decimal('quantity', 18, 4)->default(0);
			$table->decimal('price', 18, 4)->default(0);
			$table->decimal('total', 18, 4)->default(0);
			$table->text('note')->nullable();
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
		Schema::dropIfExists('purchase_return_components');
	}
};
