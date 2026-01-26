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
                'name' => 'Gudang Bandung',
                'city' => 'Bandung',
                'address' => 'Jl. Terusan Bojongsoang No. 247, Kab. Bandung, Jawa Barat',
                'description' => 'Gudang utama penyimpanan barang',
                'is_active' => true,
            ]);
            
            Warehouse::create([
                'code' => 'WH-002',
                'name' => 'Gudang Bekasi',
                'city' => 'Bekasi',
                'address' => 'Jl. Teuku Umar No. 9 RT003/RW001, Bekasi, Jawa Barat',
                'description' => 'Gudang distribusi wilayah bekasi',
                'is_active' => true,
            ]);

            Warehouse::create([
                'code' => 'WH-003',
                'name' => 'Gudang Tegal',
                'city' => 'Tegal',
                'address' => 'Jl. Prof. Moh Yamin no 77, Slawi, Jawa Tengah',
                'description' => 'Gudang distribusi wilayah tegal',
                'is_active' => true,
            ]);

            Warehouse::create([
                'code' => 'WH-004',
                'name' => 'Gudang Semarang',
                'city' => 'Semarang',
                'address' => 'Jl. Wolter Monginsidi, Genuksari, Kec. Genuk, Kota Semarang, Jawa Tengah 50117',
                'description' => 'Gudang distribusi wilayah semarang',
                'is_active' => true,
            ]);
        }
    }
}
