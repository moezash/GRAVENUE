<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    protected $table = "facilities";

    protected $fillable = [
        "name",
        "description",
        "capacity",
        "price_per_hour",
        "location",
        "image",
        "status",
        "category",
        "features",
    ];

    protected $casts = [
        "price_per_hour" => "decimal:2",
        "capacity" => "integer",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $dates = ["created_at", "updated_at"];

    // Relationships
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where("status", "available");
    }

    public function scopeUnavailable($query)
    {
        return $query->where("status", "unavailable");
    }

    public function scopeMaintenance($query)
    {
        return $query->where("status", "maintenance");
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where("category", $category);
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === "available";
    }

    public function getFormattedPriceAttribute()
    {
        return "Rp " . number_format($this->price_per_hour, 0, ",", ".");
    }

    public function getCurrentBookingsForDate($date)
    {
        return $this->bookings()
            ->where("booking_date", $date)
            ->whereIn("status", ["approved", "pending"])
            ->get();
    }
}
