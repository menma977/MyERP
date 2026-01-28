<?php

use App\Enums\ExpanseCategoryEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expanses', function (Blueprint $table) {
            $table->ulid('id')->primary()->index();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('category')->default(ExpanseCategoryEnum::SALARY->value)->index();
            $table->string('method')->default(PaymentMethodEnum::CASH->value)->index();
            /** @noinspection DuplicatedCode */
            $table->decimal('amount', 18, 4)->default(0);
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
        Schema::dropIfExists('expanses');
    }
};
