<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Polymorphic relationship to the modified entity
     */
    public function entity()
    {
        return $this->morphTo();
    }
}
