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
		Schema::create('purchase_order_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
			$table->foreignUlid('purchase_request_component_id')->constrained('purchase_request_components')->cascadeOnDelete();
			$table->ulid('purchase_procurement_component_id');
			$table->foreign('purchase_procurement_component_id', 'purchase_procurement_component_foreign')->references('id')->on('purchase_procurement_components')->onDelete('cascade');
			$table->foreignUlid('item_id')->constrained('items')->cascadeOnDelete();
			$table->decimal('request_quantity', 18, 4)->default(0);
			$table->decimal('request_price', 18, 4)->default(0);
			$table->decimal('request_total', 18, 4)->default(0);
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
		Schema::dropIfExists('purchase_order_components');
	}
};
