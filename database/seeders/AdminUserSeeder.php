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
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]
    );

    User::updateOrCreate(
        ['email' => 'manager@luckystar.com'],
        [
            'name' => 'Manager',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
        ]
    );

    User::updateOrCreate(
        ['email' => 'cashier@luckystar.com'],
        [
            'name' => 'Cashier',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
        ]
    );
}
}
