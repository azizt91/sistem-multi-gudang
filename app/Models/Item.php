<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'unit_id',
        'stock',
        'minimum_stock',
        'rack_location',
    ];

    protected $casts = [
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    /**
     * Get category of this item
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get unit of this item
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get stock transactions for this item
     */
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Check if stock is low (below minimum)
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    /**
     * Generate barcode for this item
     * Returns base64 encoded PNG
     */
    public function generateBarcode(): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($this->code, $generator::TYPE_CODE_128, 2, 60);
        return 'data:image/png;base64,' . base64_encode($barcode);
    }

    /**
     * Generate barcode as raw PNG data
     */
    public function getBarcodeRaw(): string
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode($this->code, $generator::TYPE_CODE_128, 2, 60);
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock');
    }

    /**
     * Scope for searching items
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%");
        });
    }

    /**
     * Get item by code (for barcode scanning)
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    /**
     * Generate unique item code
     */
    public static function generateCode(string $prefix = 'BRG'): string
    {
        $lastItem = self::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) substr($lastItem->code, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
