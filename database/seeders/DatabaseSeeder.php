<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        // ── Products ───────────────────────────────────────
        $products = [
            // Coffee
            ['name' => 'Brewed Coffee',           'category' => 'coffee',      'selling_price' => 60,  'cost_price' => 25, 'stock_qty' => 50, 'reorder_level' => 10, 'low_stock_threshold' => 15],
            ['name' => 'Espresso',                 'category' => 'coffee',      'selling_price' => 75,  'cost_price' => 30, 'stock_qty' => 40, 'reorder_level' => 8,  'low_stock_threshold' => 12],
            ['name' => 'Americano',                'category' => 'coffee',      'selling_price' => 90,  'cost_price' => 35, 'stock_qty' => 40, 'reorder_level' => 8,  'low_stock_threshold' => 12],
            ['name' => 'Cappuccino',               'category' => 'coffee',      'selling_price' => 110, 'cost_price' => 45, 'stock_qty' => 35, 'reorder_level' => 8,  'low_stock_threshold' => 10],
            ['name' => 'Latte',                    'category' => 'coffee',      'selling_price' => 120, 'cost_price' => 48, 'stock_qty' => 35, 'reorder_level' => 8,  'low_stock_threshold' => 10],
            ['name' => 'Flat White',               'category' => 'coffee',      'selling_price' => 130, 'cost_price' => 50, 'stock_qty' => 25, 'reorder_level' => 5,  'low_stock_threshold' => 8],

            // Iced Coffee
            ['name' => 'Iced Americano',           'category' => 'iced_coffee', 'selling_price' => 95,  'cost_price' => 38, 'stock_qty' => 50, 'reorder_level' => 10, 'low_stock_threshold' => 15],
            ['name' => 'Iced Latte',               'category' => 'iced_coffee', 'selling_price' => 130, 'cost_price' => 52, 'stock_qty' => 50, 'reorder_level' => 10, 'low_stock_threshold' => 15],
            ['name' => 'Iced Cappuccino',          'category' => 'iced_coffee', 'selling_price' => 120, 'cost_price' => 48, 'stock_qty' => 45, 'reorder_level' => 10, 'low_stock_threshold' => 14],
            ['name' => 'Iced Caramel Macchiato',   'category' => 'iced_coffee', 'selling_price' => 145, 'cost_price' => 55, 'stock_qty' => 40, 'reorder_level' => 8,  'low_stock_threshold' => 12],
            ['name' => 'Cold Brew',                'category' => 'iced_coffee', 'selling_price' => 140, 'cost_price' => 50, 'stock_qty' => 30, 'reorder_level' => 6,  'low_stock_threshold' => 10],
            ['name' => 'Frappuccino',              'category' => 'iced_coffee', 'selling_price' => 160, 'cost_price' => 60, 'stock_qty' => 30, 'reorder_level' => 6,  'low_stock_threshold' => 10],

            // Non-Coffee
            ['name' => 'Hot Chocolate',            'category' => 'non_coffee',  'selling_price' => 95,  'cost_price' => 35, 'stock_qty' => 30, 'reorder_level' => 6,  'low_stock_threshold' => 8],
            ['name' => 'Matcha Latte',             'category' => 'non_coffee',  'selling_price' => 130, 'cost_price' => 50, 'stock_qty' => 25, 'reorder_level' => 5,  'low_stock_threshold' => 8],
            ['name' => 'Strawberry Smoothie',      'category' => 'non_coffee',  'selling_price' => 120, 'cost_price' => 45, 'stock_qty' => 20, 'reorder_level' => 4,  'low_stock_threshold' => 6],
            ['name' => 'Mango Smoothie',           'category' => 'non_coffee',  'selling_price' => 120, 'cost_price' => 45, 'stock_qty' => 3,  'reorder_level' => 5, 'low_stock_threshold' => 8], // Critical stock demo
            ['name' => 'Iced Tea',                 'category' => 'non_coffee',  'selling_price' => 75,  'cost_price' => 25, 'stock_qty' => 60, 'reorder_level' => 10, 'low_stock_threshold' => 15],
            ['name' => 'Lemon Water',              'category' => 'non_coffee',  'selling_price' => 65,  'cost_price' => 20, 'stock_qty' => 0,  'reorder_level' => 5,  'low_stock_threshold' => 8],  // Out of stock demo
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(['name' => $data['name']], array_merge($data, ['is_active' => true]));
        }

        $this->command->info('✓ Lucky Star seed data created.');
        $this->command->line('  Admin: admin@luckystar.com / admin123');
        $this->command->line('  Manager: manager@luckystar.com / manager123');
        $this->command->line('  Inventory Clerk: clerk@luckystar.com / clerk123');
        $this->command->line('  Cashier: cashier@luckystar.com / cashier123');
    }
}
