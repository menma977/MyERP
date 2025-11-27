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
		Schema::create('item_batches', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->foreignUlid('item_id')->constrained('items')->cascadeOnDelete();
			$table->string('code')->unique();
			$table->timestamp('expiry_at')->nullable();
			$table->boolean('is_available')->default(true);
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
		Schema::dropIfExists('item_batches');
	}
};
