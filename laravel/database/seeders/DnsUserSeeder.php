<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DnsUser;  // Import the DnsUser model
use Illuminate\Support\Facades\Hash;

class DnsUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating some example users
        DnsUser::create([
            'username' => 'admin@admin.com',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'), // Ensure to hash the password
            'role' => 'admin',
        ]);

        DnsUser::create([
            'username' => 'user@user.com',
            'email' => 'user@user.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);
    }
}
