<?php

namespace Database\Seeders;

use App\Models\BusOperator;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // cleanup previous run if needed (optional)
        // User::where('email', 'like', 'admin.%@tiketbus.id')->delete();
        
        // 1. Create Company Admins for each Bus Operator
        $operators = BusOperator::all();
        $companyAdmins = [];
        
        foreach ($operators as $operator) {
            $email = 'admin.' . strtolower($operator->code) . '@tiketbus.id';
            
            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Admin ' . $operator->name,
                    'password' => Hash::make('password'),
                    'role' => 'company_admin',
                    'bus_operator_id' => $operator->id,
                    'phone' => '08' . mt_rand(1000000000, 9999999999),
                    'user_status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $companyAdmins[$operator->id] = $admin;
        }

        // 2. Create Terminal Admins invited by Company Admins
        // Each company will have agents at 2 random terminals
        
        $terminals = Terminal::all();
        
        foreach ($operators as $operator) {
            $admin = $companyAdmins[$operator->id];
            
            // Pick 2 random terminals
            $assignedTerminals = $terminals->random(2);
            
            foreach ($assignedTerminals as $terminal) {
                // e.g. admin.pk.kalideres@tiketbus.id
                $slug = strtolower($operator->code . '.' . Str::slug($terminal->name));
                // truncate if too long? nah. simplified:
                // admin.pk.kalideres
                // taking first word of terminal name
                $terminalSlug = Str::slug(explode(' ', $terminal->name)[0]);
                $email = 'admin.' . strtolower($operator->code) . '.' . $terminalSlug . '@tiketbus.id';
                
                if (!User::where('email', $email)->exists()) {
                    $terminalAdmin = User::create([
                        'name' => 'Petugas ' . $operator->code . ' ' . $terminal->name,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'role' => 'terminal_admin',
                        'bus_operator_id' => $operator->id, // Employee of the bus company
                        'terminal_id' => $terminal->id, // Primary terminal
                        'invited_by' => $admin->id,
                        'phone' => '08' . mt_rand(1000000000, 9999999999),
                        'user_status' => 'active',
                        'email_verified_at' => now(),
                    ]);

                    // Assign primary terminal permissions
                    $terminalAdmin->assignedTerminals()->attach($terminal->id, [
                        'assignment_type' => 'primary',
                        'can_manage_schedules' => true,
                        'can_verify_tickets' => true,
                        'can_confirm_arrivals' => true,
                    ]);
                }
            }
        }
        
        // 3. Ensure Super Admin exists
        if (!User::where('role', 'super_admin')->exists()) {
             User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@tiketbus.id',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '08999999999',
                'user_status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
    }
}
