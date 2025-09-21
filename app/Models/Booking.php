<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $table = "bookings";

    protected $fillable = [
        "facility_id",
        "user_id",
        "user_name",
        "user_email",
        "user_phone",
        "organization",
        "event_name",
        "booking_date",
        "end_date",
        "start_time",
        "end_time",
        "participants",
        "total_price",
        "additional_notes",
        "status",
    ];

    protected $casts = [
        "booking_date" => "date",
        "end_date" => "date",
        "total_price" => "decimal:2",
        "participants" => "integer",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $dates = ["booking_date", "created_at", "updated_at"];

    // Relationships
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where("status", "pending");
    }

    public function scopeApproved($query)
    {
        return $query->where("status", "approved");
    }

    public function scopeRejected($query)
    {
        return $query->where("status", "rejected");
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween("booking_date", [$startDate, $endDate]);
    }
}
