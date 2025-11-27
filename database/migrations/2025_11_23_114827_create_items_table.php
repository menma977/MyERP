<?php

use App\Enums\ItemTypeEnum;
use App\Enums\ItemUnitEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('items', function (Blueprint $table) {
			$table->ulid('id')->primary()->index();
			$table->string('code')->unique();
			$table->string('name');
			$table->string('type')->default(ItemTypeEnum::RAW->value);
			$table->string('unit')->default(ItemUnitEnum::PCS->value);
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
		Schema::dropIfExists('items');
	}
};
