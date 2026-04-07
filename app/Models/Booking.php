<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'ratehawk_order_id', 'book_hash',
        'hotel_id', 'hotel_name', 'hotel_address', 'hotel_city',
        'hotel_country', 'hotel_stars', 'hotel_image',
        'check_in', 'check_out', 'guests', 'rooms', 'rooms_data',
        'total_price', 'currency',
        'guest_first_name', 'guest_last_name', 'guest_email', 'guest_phone',
        'status', 'cancellation_policy',
    ];

    protected $casts = [
        'check_in'   => 'date',
        'check_out'  => 'date',
        'rooms_data' => 'array',
        'total_price'=> 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the number of nights for this booking.
     */
    public function getNightsAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    /**
     * Get a friendly status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'confirmed' => 'Confirmada',
            'pending'   => 'Pendiente',
            'cancelled' => 'Cancelada',
            'failed'    => 'Fallida',
            default     => ucfirst($this->status),
        };
    }

    /**
     * Get status CSS inline style (uses project design system variables).
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'confirmed' => 'color:#4caf7d; font-weight:700;',
            'pending'   => 'color:#f59e0b; font-weight:700;',
            'cancelled' => 'color:#ef4444; font-weight:700;',
            'failed'    => 'color:#b91c1c; font-weight:700;',
            default     => 'color:var(--gl); font-weight:600;',
        };
    }

    /**
     * Scope: only upcoming bookings.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>=', now()->toDateString())
                     ->where('status', 'confirmed')
                     ->orderBy('check_in');
    }

    /**
     * Scope: past bookings.
     */
    public function scopePast($query)
    {
        return $query->where('check_out', '<', now()->toDateString())
                     ->orderByDesc('check_out');
    }
}
