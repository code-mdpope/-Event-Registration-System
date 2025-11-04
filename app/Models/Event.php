<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'capacity',
        'status',
        'banner_image',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get all registrations for the event
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get all attendances for the event
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get approved registrations count
     */
    public function getApprovedRegistrationsCountAttribute()
    {
        return $this->registrations()->where('status', 'approved')->count();
    }

    /**
     * Get attendees count
     */
    public function getAttendeesCountAttribute()
    {
        return $this->attendances()->count();
    }

    /**
     * Get attendance rate
     */
    public function getAttendanceRateAttribute()
    {
        $approved = $this->approved_registrations_count;
        if ($approved == 0) {
            return 0;
        }
        return round(($this->attendees_count / $approved) * 100, 2);
    }

    /**
     * Check if event is available for registration
     */
    public function isAvailableForRegistration()
    {
        return $this->status === 'upcoming' 
            && $this->approved_registrations_count < $this->capacity
            && $this->start_date > now();
    }

    /**
     * Get banner image URL
     */
    public function getBannerImageUrlAttribute()
    {
        if ($this->banner_image) {
            return Storage::url('events/' . $this->banner_image);
        }
        return asset('images/default-event.jpg');
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
            ->where('start_date', '>', now());
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('location', 'like', "%{$search}%");
    }
}
