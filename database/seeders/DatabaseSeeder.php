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
        $this->call([
            WarehouseSeeder::class,
            CompanyProfileSeeder::class,
        ]);

        // Create Users (use firstOrCreate to avoid duplicates)
        $admin = User::firstOrCreate(
            ['email' => 'admin@warehouse.test'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@warehouse.test'],
            [
                'name' => 'Staff Gudang',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]
        );

        User::firstOrCreate(
            ['email' => 'owner@warehouse.test'],
            [
                'name' => 'Owner',
                'password' => bcrypt('password'),
                'role' => 'owner',
            ]
        );

        // Create Categories
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Barang-barang elektronik'],
            ['name' => 'Bahan Bangunan', 'description' => 'Material konstruksi dan bangunan'],
            ['name' => 'Alat Tulis', 'description' => 'Perlengkapan kantor dan alat tulis'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk konsumsi'],
            ['name' => 'Peralatan', 'description' => 'Alat-alat dan tools'],
        ];

        // Create Categories (idempotent)
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Barang-barang elektronik'],
            ['name' => 'Bahan Bangunan', 'description' => 'Material konstruksi dan bangunan'],
            ['name' => 'Alat Tulis', 'description' => 'Perlengkapan kantor dan alat tulis'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk konsumsi'],
            ['name' => 'Peralatan', 'description' => 'Alat-alat dan tools'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create Units (idempotent)
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
            Unit::firstOrCreate(['abbreviation' => $unit['abbreviation']], $unit);
        }

        // Create Sample Items (idempotent - check by code)
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
            Item::firstOrCreate(['code' => $item['code']], $item);
        }

        // Distribute stock across all warehouses
        $warehouses = \App\Models\Warehouse::all();
        
        if ($warehouses->count() > 0 && StockTransaction::count() === 0) {
            foreach ($warehouses as $warehouse) {
                // Determine which items are stocked in this warehouse (e.g., 80% of items)
                foreach ($items as $itemData) {
                    // Randomly skip some items for variety, but ensure at least some common ones exist
                    // Let's say 70% chance an item exists in a warehouse
                    if (rand(1, 100) > 70) continue;

                    $item = Item::where('code', $itemData['code'])->first();
                    if (!$item) continue;

                    // Generate random stock
                    $quantity = rand(10, 100);
                    $date = now()->subDays(rand(1, 30))->subHours(rand(1, 12));
                    
                    // Create partial low stock scenarios
                    if (rand(1, 10) > 8) {
                        $quantity = rand(1, 5); // Low stock scenario
                    }

                    // Create Header
                    $header = \App\Models\StockHeader::create([
                        'document_number' => 'DOC-' . $warehouse->code . '-' . date('Ymd', strtotime($date)) . '-' . rand(1000, 9999),
                        'type' => 'in',
                        'transaction_date' => $date,
                        'user_id' => $admin->id,
                        'notes' => 'Stok awal ' . $warehouse->name,
                        'warehouse_id' => $warehouse->id,
                    ]);
                    
                    // Create Transaction
                    StockTransaction::create([
                        'stock_header_id' => $header->id,
                        'item_id' => $item->id,
                        'user_id' => $admin->id,
                        'type' => 'in',
                        'quantity' => $quantity,
                        'stock_before' => 0,
                        'stock_after' => $quantity,
                        'notes' => 'Setup awal',
                        'transaction_date' => $date,
                    ]);

                    // Update Warehouse Item Stock
                    \App\Models\WarehouseItem::updateOrCreate(
                        ['warehouse_id' => $warehouse->id, 'item_id' => $item->id],
                        [
                            'stock' => $quantity, 
                            'minimum_stock' => $item->minimum_stock ?? 10
                        ]
                    );
                }
            }
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
