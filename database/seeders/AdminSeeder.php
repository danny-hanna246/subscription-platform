<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
        ]);
    }
}
