<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('uploaded_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('subject')->nullable();
            $table->string('description')->nullable();

            $table->string('file_path');
            $table->string('file_type', 50)->nullable(); // e.g. pdf, docx, image
            $table->unsignedBigInteger('file_size_bytes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};