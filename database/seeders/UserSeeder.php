<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Owner',
            'email' => 'admin@luckystar.com',
            'password' => bcrypt('admin123'),
            'role' => 'owner',
        ]);

        \App\Models\User::create([
            'name' => 'Cashier',
            'email' => 'cashier@luckystar.com',
            'password' => bcrypt('cashier123'),
            'role' => 'cashier',
        ]);

        \App\Models\User::create([
            'name' => 'Manager',
            'email' => 'manager@luckystar.com',
            'password' => bcrypt('manager123'),
            'role' => 'manager',
        ]);
    }
}
