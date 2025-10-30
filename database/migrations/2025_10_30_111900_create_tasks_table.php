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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('due_date');
            $table->enum('repeat_mode', ['none', 'daily', 'selected'])->default('none');
            $table->json('repeat_days')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('due_date');
            $table->index('repeat_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
