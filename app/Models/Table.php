<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model describing a physical table in the restaurant
class Table extends Model
{
    use HasFactory;

    // Allow mass assignment for admin management screens
    protected $fillable = [
        'number',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sessions()
    {
        return $this->hasMany(TableSession::class);
    }
}
