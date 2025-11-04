<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'registration_date',
        'status',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

    /**
     * Get the user that owns the registration
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that owns the registration
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Check if registration is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if registration can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->status !== 'cancelled' 
            && $this->event->start_date > now();
    }
}
