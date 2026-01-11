<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get items in this category
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get items count
     */
    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }
}
