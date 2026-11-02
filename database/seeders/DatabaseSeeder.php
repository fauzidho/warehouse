<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database without dummy inventory data.
     */
    public function run(): void
    {
        // Default Clean Accounts (Admin & Pegawai)
        User::firstOrCreate(
            ['email' => 'admin@halfkopi.com'],
            [
                'name' => 'Admin Half Kopi',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0812-3456-7890',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'pegawai@halfkopi.com'],
            [
                'name' => 'Pegawai Gudang',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'phone' => '0811-2233-4455',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'barista@halfkopi.com'],
            [
                'name' => 'Barista (Staff Operasional)',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'phone' => '0813-9876-5432',
                'is_active' => true,
            ]
        );
    }
}
