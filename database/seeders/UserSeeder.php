<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@tiketbus.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // Verifier users (terminal staff)
        User::create([
            'name' => 'Petugas Terminal Kalideres',
            'email' => 'verifier.kalideres@tiketbus.id',
            'password' => Hash::make('password'),
            'role' => 'verifier',
            'phone' => '081234567891',
        ]);

        User::create([
            'name' => 'Petugas Terminal Purabaya',
            'email' => 'verifier.purabaya@tiketbus.id',
            'password' => Hash::make('password'),
            'role' => 'verifier',
            'phone' => '081234567892',
        ]);

        // Demo buyer users
        User::create([
            'name' => 'Ahmad Rasyid',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081234567893',
        ]);

        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081234567894',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081234567895',
        ]);
    }
}
