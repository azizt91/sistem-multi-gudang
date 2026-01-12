<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_STAFF = 'staff';
    const ROLE_OWNER = 'owner';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'warehouse_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * Check if user is owner
     */
    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    // Access Check Helper
    public function hasWarehouseAccess($warehouseId)
    {
        if ($this->isAdmin() || $this->isOwner()) {
            return true; // Admin/Owner can access all
        }
        
        // Staff can only access their assigned warehouse
        return $this->warehouse_id == $warehouseId;
    }

    /**
     * Check if user can create transactions
     */
    public function canCreateTransaction(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_STAFF]);
    }

    /**
     * Check if user can edit transactions
     */
    public function canEditTransaction(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user can delete transactions
     */
    public function canDeleteTransaction(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Get stock transactions created by user
     */
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_STAFF => 'Staff Gudang',
            self::ROLE_OWNER => 'Owner',
        ];
    }
}
