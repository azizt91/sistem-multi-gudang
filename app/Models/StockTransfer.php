<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'source_warehouse_id',
        'destination_warehouse_id',
        'status',
        'notes',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            $prefix = 'TRF-' . now()->format('Ymd') . '-';
            $lastTransfer = self::where('transfer_number', 'like', $prefix . '%')
                ->orderBy('transfer_number', 'desc')
                ->first();

            if ($lastTransfer) {
                $lastNumber = (int) substr($lastTransfer->transfer_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $transfer->transfer_number = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockHeaders()
    {
        return $this->hasMany(StockHeader::class);
    }
}
