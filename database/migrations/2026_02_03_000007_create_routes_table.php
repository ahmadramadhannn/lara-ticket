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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_terminal_id')->constrained('terminals')->cascadeOnDelete();
            $table->foreignId('destination_terminal_id')->constrained('terminals')->cascadeOnDelete();
            $table->integer('distance_km')->nullable();
            $table->integer('estimated_duration_minutes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['origin_terminal_id', 'destination_terminal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
