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
        Schema::table('food_entries', function (Blueprint $table) {
            $table->foreignId('food_template_id')
                ->nullable()
                ->constrained('food_templates')
                ->nullOnDelete()
                ->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('food_template_id');
        });
    }
};
