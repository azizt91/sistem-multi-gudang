<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'email',
        'website',
        'logo_path',
    ];

    // Singleton pattern helper
    public static function get()
    {
        return self::firstOrCreate(
            ['id' => 1],
            ['company_name' => 'PT. Morasha Inti Shabira']
        );
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getFaviconUrlAttribute()
    {
        // Future: Check if custom favicon_path exists in DB
        // return $this->favicon_path ? asset('storage/' . $this->favicon_path) : asset('storage/company/favicon.png');
        
        return asset('storage/company/favicon.png');
    }
}
