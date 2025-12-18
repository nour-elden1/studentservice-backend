<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_groups');
    }
};