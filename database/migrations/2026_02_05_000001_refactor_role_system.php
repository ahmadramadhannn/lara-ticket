<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration refactors the role system:
     * - Changes role column from enum to string for flexibility
     * - Migrates 'operator' role to 'company_admin'
     * - Adds 'terminal_id' for terminal_admin primary assignment
     */
    public function up(): void
    {
        // Step 1: For SQLite compatibility, we need to handle enum differently
        // First, let's check the driver
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN, so we work around it
            // The role column will accept any string value in SQLite
            // We just need to migrate the data
            DB::table('users')->where('role', 'operator')->update(['role' => 'company_admin']);
        } elseif ($driver === 'pgsql') {
            // Postgres uses different syntax for altering columns
            DB::table('users')->where('role', 'operator')->update(['role' => 'company_admin']);
            
            // Alter the column type and default value separately
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(30)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'buyer'");
        } else {
            // For MySQL, we need to alter the enum
            // First migrate existing usage
            DB::table('users')->where('role', 'operator')->update(['role' => 'company_admin']);
            
            // Then alter the column to allow new values
            // Using raw SQL for enum modification
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) DEFAULT 'buyer'");
        }

        // Step 2: Add terminal_id column for terminal_admin primary terminal
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'terminal_id')) {
                $table->unsignedBigInteger('terminal_id')->nullable()->after('bus_operator_id');
                $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove terminal_id column
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['terminal_id']);
            $table->dropColumn('terminal_id');
        });

        // Revert role changes
        DB::table('users')->where('role', 'company_admin')->update(['role' => 'operator']);
        DB::table('users')->where('role', 'terminal_admin')->update(['role' => 'operator']);
    }
};
