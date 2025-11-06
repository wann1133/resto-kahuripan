<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Order header with status machine
class Order extends Model
{
    use HasFactory;

    public const STATUS_PLACED = 'PLACED';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_READY = 'READY';
    public const STATUS_SERVED = 'SERVED';
    public const STATUS_PAID = 'PAID';
    public const STATUS_CLOSED = 'CLOSED';

    protected $fillable = [
        'table_session_id',
        'code',
        'subtotal',
        'tax',
        'service_charge',
        'grand_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function tableSession(): BelongsTo
    {
        return $this->belongsTo(TableSession::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
