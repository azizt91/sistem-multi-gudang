<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (CompanyProfile::count() === 0) {
            CompanyProfile::create([
                'company_name' => 'PT. Morasha Inti Shabira',
                'address' => 'Jl. Pungkur No. 220-218, Bandung, Jawa Barat',
                'phone' => '+62 813-2222-5484',
                'email' => 'ptmoisha@gmail.com',
                'website' => 'https://www.ptmorasha.com',
                'logo_path' => null, 
            ]);
        }
    }
}
