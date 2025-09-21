<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;

// Public routes
Route::get("/", [PageController::class, "home"])->name("home");
Route::get("/about", [PageController::class, "about"])->name("about");
Route::get("/contact", [PageController::class, "contact"])->name("contact");
Route::post("/contact", [PageController::class, "submitContact"])->name("contact.submit");
Route::get("/schedule", [PageController::class, "schedule"])->name("schedule");

// User Authentication routes
Route::get("/login", [AuthController::class, "showLogin"])->name("login");
Route::post("/login", [AuthController::class, "login"]);
Route::get("/register", [AuthController::class, "showRegister"])->name(
    "register",
);
Route::post("/register", [AuthController::class, "register"]);

// Protected user routes
Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [AuthController::class, "dashboard"])->name(
        "dashboard",
    );
    Route::get("/profile", [AuthController::class, "profile"])->name("profile");
    Route::post("/profile", [AuthController::class, "updateProfile"]);
    Route::post("/change-password", [
        AuthController::class,
        "changePassword",
    ])->name("change-password");
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
});

// Public facility routes
Route::get("/facilities", [PageController::class, "facilities"])->name(
    "facilities",
);
Route::get("/facilities/{id}", [PageController::class, "facility"])->name(
    "facility",
);

// Booking routes (require authentication)
Route::middleware("auth")->group(function () {
    Route::get("/booking/{facilityId}", [
        PageController::class,
        "bookingForm",
    ])->name("booking.form");
    Route::post("/booking", [PageController::class, "submitBooking"])->name(
        "booking.submit",
    );
});

// Public booking status and payment routes
Route::get("/booking-status/{id}", [
    PageController::class,
    "bookingStatus",
])->name("booking.status");
Route::get("/payment/{id}", [PageController::class, "payment"])->name(
    "payment",
);
Route::post("/payment/{id}", [PageController::class, "processPayment"])->name(
    "payment.process",
);
Route::post("/payment/notification", [PageController::class, "paymentNotification"])->name(
    "payment.notification",
);

// Midtrans testing route
Route::get("/test-midtrans", function () {
    try {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'TEST-' . time(),
                'gross_amount' => 100000,
            ],
            'customer_details' => [
                'first_name' => 'Test User',
                'email' => 'test@gravenue.com',
                'phone' => '081234567890',
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Midtrans integration working!',
            'snap_token' => $snapToken,
            'config' => [
                'server_key' => config('midtrans.server_key'),
                'client_key' => config('midtrans.client_key'),
                'is_production' => config('midtrans.is_production'),
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'config' => [
                'server_key' => config('midtrans.server_key'),
                'client_key' => config('midtrans.client_key'),
                'is_production' => config('midtrans.is_production'),
            ]
        ], 500);
    }
})->name("test.midtrans");

// Admin routes
Route::prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/", function () {
            return redirect()->route("admin.dashboard");
        });
        Route::get("/login", [AdminController::class, "login"])->name("login");
        Route::post("/login", [AdminController::class, "authenticate"])->name(
            "authenticate",
        );
        Route::get("/logout", [AdminController::class, "logout"])->name(
            "logout",
        );

        // Temporary route for testing
        Route::get("/test-dashboard", [
            AdminController::class,
            "dashboard",
        ])->name("test.dashboard");

        Route::middleware("auth:admin")->group(function () {
            Route::get("/dashboard", [
                AdminController::class,
                "dashboard",
            ])->name("dashboard");

            // Facilities management
            Route::get("/facilities", [
                AdminController::class,
                "facilities",
            ])->name("facilities");
            Route::get("/facilities/create", [
                AdminController::class,
                "createFacility",
            ])->name("facilities.create");
            Route::post("/facilities", [
                AdminController::class,
                "storeFacility",
            ])->name("facilities.store");
            Route::get("/facilities/{id}/edit", [
                AdminController::class,
                "editFacility",
            ])->name("facilities.edit");
            Route::put("/facilities/{id}", [
                AdminController::class,
                "updateFacility",
            ])->name("facilities.update");
            Route::delete("/facilities/{id}", [
                AdminController::class,
                "deleteFacility",
            ])->name("facilities.delete");

            // Bookings management
            Route::get("/bookings", [AdminController::class, "bookings"])->name(
                "bookings",
            );
            Route::get("/bookings/{id}", [
                AdminController::class,
                "bookingDetail",
            ])->name("booking.detail");
            Route::post("/bookings/{id}/status", [
                AdminController::class,
                "updateBookingStatus",
            ])->name("booking.update-status");

            // Payments management
            Route::get("/payments", [AdminController::class, "payments"])->name(
                "payments",
            );

            // Schedule management
            Route::get("/schedule", [AdminController::class, "schedule"])->name(
                "schedule",
            );

            // Reports
            Route::get("/reports", [AdminController::class, "reports"])->name(
                "reports",
            );

            // Messages/Contact management
            Route::get("/messages", [AdminController::class, "messages"])->name(
                "messages",
            );
            Route::get("/messages/{id}", [AdminController::class, "messageDetail"])->name(
                "messages.detail",
            );
            Route::post("/messages/{id}/reply", [AdminController::class, "replyMessage"])->name(
                "messages.reply",
            );
            Route::post("/messages/{id}/mark-read", [AdminController::class, "markMessageRead"])->name(
                "messages.mark-read",
            );
        });
    });
