<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_resources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('study_group_id')
                ->constrained('study_groups')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('description')->nullable();

            $table->enum('type', ['file', 'link'])
                ->default('file');

            $table->string('file_path')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();

            $table->string('link')->nullable();

            // visibility: group-only or shared globally across user ("Shared Resources" tab)
            $table->enum('visibility', ['group', 'shared'])
                ->default('group')
                ->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index('study_group_id');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_resources');
    }
};