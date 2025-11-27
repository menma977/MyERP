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
		Schema::create('purchase_request_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
			$table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
			$table->decimal('price', 18, 4)->default(0);
			$table->decimal('quantity', 18, 4)->default(0);
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
		Schema::dropIfExists('purchase_request_components');
	}
};
