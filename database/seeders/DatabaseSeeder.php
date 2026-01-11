<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@warehouse.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff Gudang',
            'email' => 'staff@warehouse.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Owner',
            'email' => 'owner@warehouse.test',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Barang-barang elektronik'],
            ['name' => 'Bahan Bangunan', 'description' => 'Material konstruksi dan bangunan'],
            ['name' => 'Alat Tulis', 'description' => 'Perlengkapan kantor dan alat tulis'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk konsumsi'],
            ['name' => 'Peralatan', 'description' => 'Alat-alat dan tools'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Units
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'Pcs'],
            ['name' => 'Kilogram', 'abbreviation' => 'Kg'],
            ['name' => 'Gram', 'abbreviation' => 'g'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Meter', 'abbreviation' => 'm'],
            ['name' => 'Box', 'abbreviation' => 'Box'],
            ['name' => 'Pack', 'abbreviation' => 'Pack'],
            ['name' => 'Lusin', 'abbreviation' => 'Lsn'],
            ['name' => 'Karung', 'abbreviation' => 'Krg'],
            ['name' => 'Roll', 'abbreviation' => 'Roll'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        // Create Sample Items
        $items = [
            ['code' => 'BRG00001', 'name' => 'Laptop Asus VivoBook', 'category_id' => 1, 'unit_id' => 1, 'stock' => 25, 'minimum_stock' => 5, 'rack_location' => 'A-01'],
            ['code' => 'BRG00002', 'name' => 'Mouse Wireless Logitech', 'category_id' => 1, 'unit_id' => 1, 'stock' => 50, 'minimum_stock' => 10, 'rack_location' => 'A-02'],
            ['code' => 'BRG00003', 'name' => 'Keyboard Mechanical', 'category_id' => 1, 'unit_id' => 1, 'stock' => 30, 'minimum_stock' => 8, 'rack_location' => 'A-03'],
            ['code' => 'BRG00004', 'name' => 'Semen Portland 50Kg', 'category_id' => 2, 'unit_id' => 9, 'stock' => 100, 'minimum_stock' => 20, 'rack_location' => 'B-01'],
            ['code' => 'BRG00005', 'name' => 'Pasir Halus', 'category_id' => 2, 'unit_id' => 2, 'stock' => 500, 'minimum_stock' => 100, 'rack_location' => 'B-02'],
            ['code' => 'BRG00006', 'name' => 'Besi Beton 10mm', 'category_id' => 2, 'unit_id' => 5, 'stock' => 200, 'minimum_stock' => 50, 'rack_location' => 'B-03'],
            ['code' => 'BRG00007', 'name' => 'Kertas HVS A4 80gsm', 'category_id' => 3, 'unit_id' => 7, 'stock' => 150, 'minimum_stock' => 30, 'rack_location' => 'C-01'],
            ['code' => 'BRG00008', 'name' => 'Pulpen Standard', 'category_id' => 3, 'unit_id' => 8, 'stock' => 3, 'minimum_stock' => 10, 'rack_location' => 'C-02'], // Low stock
            ['code' => 'BRG00009', 'name' => 'Buku Tulis 38 Lembar', 'category_id' => 3, 'unit_id' => 8, 'stock' => 100, 'minimum_stock' => 20, 'rack_location' => 'C-03'],
            ['code' => 'BRG00010', 'name' => 'Air Mineral 600ml', 'category_id' => 4, 'unit_id' => 6, 'stock' => 5, 'minimum_stock' => 10, 'rack_location' => 'D-01'], // Low stock
            ['code' => 'BRG00011', 'name' => 'Mie Instan', 'category_id' => 4, 'unit_id' => 6, 'stock' => 80, 'minimum_stock' => 20, 'rack_location' => 'D-02'],
            ['code' => 'BRG00012', 'name' => 'Kopi Sachet', 'category_id' => 4, 'unit_id' => 6, 'stock' => 120, 'minimum_stock' => 25, 'rack_location' => 'D-03'],
            ['code' => 'BRG00013', 'name' => 'Obeng Set', 'category_id' => 5, 'unit_id' => 1, 'stock' => 20, 'minimum_stock' => 5, 'rack_location' => 'E-01'],
            ['code' => 'BRG00014', 'name' => 'Tang Kombinasi', 'category_id' => 5, 'unit_id' => 1, 'stock' => 15, 'minimum_stock' => 5, 'rack_location' => 'E-02'],
            ['code' => 'BRG00015', 'name' => 'Kunci Inggris 12"', 'category_id' => 5, 'unit_id' => 1, 'stock' => 2, 'minimum_stock' => 5, 'rack_location' => 'E-03'], // Low stock
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        // Create sample transactions
        $sampleTransactions = [
            ['item_id' => 1, 'type' => 'in', 'quantity' => 10, 'notes' => 'Pembelian awal'],
            ['item_id' => 1, 'type' => 'out', 'quantity' => 2, 'notes' => 'Pengiriman ke cabang A'],
            ['item_id' => 2, 'type' => 'in', 'quantity' => 25, 'notes' => 'Restok dari supplier'],
            ['item_id' => 4, 'type' => 'in', 'quantity' => 50, 'notes' => 'Pembelian semen'],
            ['item_id' => 4, 'type' => 'out', 'quantity' => 10, 'notes' => 'Pengiriman proyek X'],
            ['item_id' => 7, 'type' => 'in', 'quantity' => 30, 'notes' => 'Pembelian kertas bulanan'],
            ['item_id' => 10, 'type' => 'out', 'quantity' => 5, 'notes' => 'Pemakaian internal'],
        ];

        foreach ($sampleTransactions as $index => $txData) {
            $item = Item::find($txData['item_id']);
            $stockBefore = $item->stock;
            
            if ($txData['type'] === 'in') {
                $stockAfter = $stockBefore; // Stock already set in items
            } else {
                $stockAfter = $stockBefore; // Stock already set in items
            }

            StockTransaction::create([
                'item_id' => $txData['item_id'],
                'user_id' => $admin->id,
                'type' => $txData['type'],
                'quantity' => $txData['quantity'],
                'stock_before' => $stockBefore - ($txData['type'] === 'in' ? $txData['quantity'] : -$txData['quantity']),
                'stock_after' => $stockBefore,
                'notes' => $txData['notes'],
                'transaction_date' => now()->subDays(rand(0, 6))->subHours(rand(1, 12)),
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('-------------------');
        $this->command->info('Admin: admin@warehouse.test / password');
        $this->command->info('Staff: staff@warehouse.test / password');
        $this->command->info('Owner: owner@warehouse.test / password');
    }
}
