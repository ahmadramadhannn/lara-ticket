<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_operator_id')->nullable()->after('phone');
            $table->string('user_status', 20)->default('active')->after('bus_operator_id'); // active, pending, suspended
        });

        // Step 2: Migrate existing roles - convert 'admin' and 'verifier' to 'operator'
        DB::table('users')->where('role', 'admin')->update(['role' => 'operator']);
        DB::table('users')->where('role', 'verifier')->update(['role' => 'operator']);

        // Step 3: Add columns to bus_operators table
        Schema::table('bus_operators', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('pending')->after('is_active'); // pending, approved, rejected
            $table->unsignedBigInteger('submitted_by')->nullable()->after('approval_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('submitted_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Step 4: Add foreign key for bus_operator_id on users
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('bus_operator_id')->references('id')->on('bus_operators')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bus_operator_id']);
            $table->dropColumn(['bus_operator_id', 'user_status']);
        });

        // Revert role changes
        DB::table('users')->where('role', 'operator')->update(['role' => 'buyer']);
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'buyer']);

        Schema::table('bus_operators', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'submitted_by', 'approved_by', 'approved_at']);
        });
    }
};
