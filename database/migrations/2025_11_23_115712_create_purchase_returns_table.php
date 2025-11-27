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
		Schema::create('purchase_returns', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
			$table->foreignUlid('good_receipt_id')->constrained('good_receipts')->cascadeOnDelete();
			$table->string('code')->unique();
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
		Schema::dropIfExists('purchase_returns');
	}
};
