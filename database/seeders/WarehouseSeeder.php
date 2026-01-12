<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if any warehouse exists
        if (Warehouse::count() === 0) {
            Warehouse::create([
                'code' => 'WH-001',
                'name' => 'Gudang Jabar',
                'city' => 'Bandung',
                'address' => 'Jl. Terusan Bojongsoang No. 247, Kab. Bandung, Jawa Barat',
                'description' => 'Gudang utama penyimpanan barang',
                'is_active' => true,
            ]);
            
            Warehouse::create([
                'code' => 'WH-002',
                'name' => 'Gudang Jabo',
                'city' => 'Bekasi',
                'address' => 'Jl. Teuku Umar No. 9 RT003/RW001, Bekasi, Jawa Barat',
                'description' => 'Gudang distribusi wilayah jabodetabek',
                'is_active' => true,
            ]);

            Warehouse::create([
                'code' => 'WH-003',
                'name' => 'Gudang Jateng',
                'city' => 'Tegal',
                'address' => 'Jl. Prof. Moh Yamin no 77, Slawi, Jawa Tengah',
                'description' => 'Gudang distribusi wilayah jateng',
                'is_active' => true,
            ]);
        }
    }
}
