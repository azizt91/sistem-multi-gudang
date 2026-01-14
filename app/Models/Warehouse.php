<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
        'address',
        'description',
        'is_active',
        'pic',
        'phone',
    ];

    protected static function booted()
    {
        static::creating(function ($warehouse) {
            if (empty($warehouse->code)) {
                $latest = static::latest('id')->first();
                $nextId = $latest ? $latest->id + 1 : 1;
                $warehouse->code = 'WH-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    public function stockHeaders()
    {
        return $this->hasMany(StockHeader::class);
    }
}
