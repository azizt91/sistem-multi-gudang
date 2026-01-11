<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    /**
     * Get items using this unit
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get formatted name with abbreviation
     */
    public function getFormattedNameAttribute()
    {
        return "{$this->name} ({$this->abbreviation})";
    }
}
