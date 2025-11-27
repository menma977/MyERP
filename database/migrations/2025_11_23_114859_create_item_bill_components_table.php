<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('item_bill_components', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('item_bill_id')->constrained('item_bills')->cascadeOnDelete();
			$table->foreignUlid('item_id')->constrained('items')->cascadeOnDelete();
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
		Schema::dropIfExists('item_bill_components');
	}
};
