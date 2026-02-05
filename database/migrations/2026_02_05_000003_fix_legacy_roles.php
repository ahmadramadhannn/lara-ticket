<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix legacy roles that weren't migrated properly.
     * - 'admin' -> 'company_admin'
     * - 'verifier' -> 'terminal_admin' (since they are terminal-level staff)
     * - 'operator' -> 'company_admin' (if any remaining)
     */
    public function up(): void
    {
        // Convert admin to company_admin
        DB::table('users')->where('role', 'admin')->update(['role' => 'company_admin']);
        
        // Convert verifier to terminal_admin (they work at terminals)
        DB::table('users')->where('role', 'verifier')->update(['role' => 'terminal_admin']);
        
        // Convert any remaining operator to company_admin
        DB::table('users')->where('role', 'operator')->update(['role' => 'company_admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old roles
        DB::table('users')->where('role', 'company_admin')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'terminal_admin')->update(['role' => 'verifier']);
    }
};
