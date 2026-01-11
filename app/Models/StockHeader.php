<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHeader extends Model
{
    use HasFactory;

    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    protected $fillable = [
        'document_number',
        'type',
        'transaction_date',
        'notes',
        'sender_name',
        'receiver_name',
        'sender_signature',
        'receiver_signature',
        'receipt_locked',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'receipt_locked' => 'boolean',
    ];

    /**
     * Boot method to generate document number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->document_number)) {
                $model->document_number = self::generateDocumentNumber($model->type);
            }
        });
    }

    /**
     * Generate unique document number
     */
    public static function generateDocumentNumber(string $type): string
    {
        $prefix = $type === self::TYPE_IN ? 'IN' : 'OUT';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->where('type', $type)->count() + 1;
        
        return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user who created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transaction details/items
     */
    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
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
     * Check if receipt is locked
     */
    public function isReceiptLocked(): bool
    {
        return $this->receipt_locked;
    }

    /**
     * Check if receipt has both signatures
     */
    public function hasCompleteSignatures(): bool
    {
        return $this->sender_signature && $this->receiver_signature;
    }

    /**
     * Get sender signature URL
     */
    public function getSenderSignatureUrlAttribute(): ?string
    {
        return $this->sender_signature ? asset('storage/' . $this->sender_signature) : null;
    }

    /**
     * Get receiver signature URL
     */
    public function getReceiverSignatureUrlAttribute(): ?string
    {
        return $this->receiver_signature ? asset('storage/' . $this->receiver_signature) : null;
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->transactions()->count();
    }

    /**
     * Get total quantity
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->transactions()->sum('quantity');
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

    /**
     * Scope for stock in
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    /**
     * Scope for stock out
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    /**
     * Scope for today's transactions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }
}
