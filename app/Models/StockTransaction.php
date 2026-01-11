<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    protected $fillable = [
        'stock_header_id',
        'item_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'transaction_date' => 'datetime',
    ];

    /**
     * Get the stock header (document) for this transaction
     */
    public function stockHeader()
    {
        return $this->belongsTo(StockHeader::class);
    }

    /**
     * Get the item for this transaction
     */
    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the user who created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for stock in transactions
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    /**
     * Scope for stock out transactions
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    /**
     * Scope for date range filter
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope for today's transactions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }

    /**
     * Scope for this month's transactions
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                     ->whereYear('transaction_date', now()->year);
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_IN ? 'Stok Masuk' : 'Stok Keluar';
    }

    /**
     * Get transaction type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return $this->type === self::TYPE_IN ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get available types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_IN => 'Stok Masuk',
            self::TYPE_OUT => 'Stok Keluar',
        ];
    }
}
