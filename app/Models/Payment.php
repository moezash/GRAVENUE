<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = "payments";

    protected $fillable = [
        "booking_id",
        "payment_amount",
        "payment_method",
        "payment_status",
        "payment_date",
        "transaction_id",
        "notes",
    ];

    protected $casts = [
        "payment_amount" => "decimal:2",
        "payment_date" => "datetime",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $dates = ["payment_date", "created_at", "updated_at"];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where("payment_status", "pending");
    }

    public function scopePaid($query)
    {
        return $query->whereIn("payment_status", ["paid", "paid_dummy"]);
    }

    public function scopeRefunded($query)
    {
        return $query->where("payment_status", "refunded");
    }

    public function scopeFailed($query)
    {
        return $query->where("payment_status", "failed");
    }
}
