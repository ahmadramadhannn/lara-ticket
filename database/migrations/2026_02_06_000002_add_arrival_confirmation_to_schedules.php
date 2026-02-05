<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add arrival confirmation columns to schedules table.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('arrival_status', 20)->default('pending')->after('status'); // pending, confirmed, rejected
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('arrival_status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            $table->text('confirmation_notes')->nullable()->after('confirmed_at');
            
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn(['arrival_status', 'confirmed_by', 'confirmed_at', 'confirmation_notes']);
        });
    }
};
