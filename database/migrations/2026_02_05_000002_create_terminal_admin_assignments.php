<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the terminal_user pivot table for many-to-many relationship
     * between terminal_admin users and terminals they manage.
     */
    public function up(): void
    {
        Schema::create('terminal_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('terminal_id')->constrained()->onDelete('cascade');
            
            // Assignment type: 'primary' for main terminal, 'backup' for coverage
            $table->string('assignment_type', 20)->default('primary');
            
            // Granular permissions
            $table->boolean('can_manage_schedules')->default(true);
            $table->boolean('can_verify_tickets')->default(true);
            $table->boolean('can_confirm_arrivals')->default(false); // For destination terminal
            
            $table->timestamps();
            
            // Each user can only be assigned to a terminal once
            $table->unique(['user_id', 'terminal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_user');
    }
};
