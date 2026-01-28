<?php

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
        Schema::create('file_buckets', function (Blueprint $table) {
            $table->ulid('id')->primary()->index();
            $table->text('model_type');
            $table->text('model_id');
            $table->string('name');
            $table->text('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('mime')->nullable();
            $table->string('extension')->nullable();
            $table->decimal('size', 18, 4)->default(0);
            $table->json('data')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('finished_at')->nullable();
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
        Schema::dropIfExists('file_buckets');
    }
};
