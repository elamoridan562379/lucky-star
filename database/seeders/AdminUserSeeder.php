<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
{
    User::updateOrCreate(
        ['email' => 'admin@luckystar.com'],
        [
            'name' => 'System Admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]
    );

    User::updateOrCreate(
        ['email' => 'manager@luckystar.com'],
        [
            'name' => 'Maria Santos',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
        ]
    );

    User::updateOrCreate(
        ['email' => 'clerk@luckystar.com'],
        [
            'name' => 'Ana Reyes',
            'password' => Hash::make('clerk123'),
            'role' => 'inventory_clerk',
        ]
    );

    User::updateOrCreate(
        ['email' => 'cashier@luckystar.com'],
        [
            'name' => 'Juan Dela Cruz',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
        ]
    );
}
}
