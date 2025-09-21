<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = "admin";

    protected $fillable = ["username", "password", "email", "full_name"];

    protected $hidden = ["password", "remember_token"];

    protected $casts = [
        "password" => "hashed",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $dates = ["created_at", "updated_at"];

    // Guard name for multi-auth
    protected $guard = "admin";

    /**
     * Get the bookings managed by this admin.
     */
    public function managedBookings()
    {
        return $this->hasMany(Booking::class, "approved_by");
    }

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === "super_admin";
    }

    /**
     * Scope for super admins only
     */
    public function scopeSuperAdmin($query)
    {
        return $query->where("role", "super_admin");
    }
}
