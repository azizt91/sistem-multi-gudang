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
        'image',
        'rack_location',
    ];

    protected $casts = [
        // 'stock' removed from casts since it's no longer a column
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

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    public function getStockInWarehouse($warehouseId)
    {
        return $this->warehouseItems()->where('warehouse_id', $warehouseId)->first()?->stock ?? 0;
    }

    /**
     * Get stock transactions for this item
     */
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get total stock across all warehouses
     */
    public function getStockAttribute()
    {
        return $this->warehouseItems->sum('stock');
    }

    /**
     * Get minimum stock (using max of warehouse minimums or sum? Usually per warehouse but for global view maybe sum or max?)
     * For now let's sum it or return 0 if no warehouse items.
     */
    public function getMinimumStockAttribute()
    {
        return $this->warehouseItems->sum('minimum_stock');
    }

    /**
     * Check if stock is low (below minimum) in ANY warehouse
     */
    public function isLowStock(): bool
    {
        // Return true if ANY warehouse has low stock? Or if Total < Total Min?
        // Usually low stock is per warehouse.
        // Let's say if it's low in ANY warehouse.
        foreach ($this->warehouseItems as $wi) {
            if ($wi->stock <= $wi->minimum_stock) {
                return true;
            }
        }
        return false;
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
     * Scope for low stock items (in any warehouse)
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('warehouseItems', function ($q) {
            $q->whereColumn('stock', '<=', 'minimum_stock');
        });
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
