<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin(Request $request)
    {
        // Store intended URL if provided
        if ($request->has("intended")) {
            session(["url.intended" => $request->get("intended")]);
        }

        $message = null;
        if ($request->get("message") === "login_required") {
            $message = "Silakan login terlebih dahulu untuk melakukan booking.";
        }

        return view("auth.login", compact("message"));
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if (
            Auth::attempt(
                $request->only("email", "password"),
                $request->boolean("remember"),
            )
        ) {
            $request->session()->regenerate();

            // Check for intended URL from session storage or query parameter
            $intendedUrl = $request->get("intended") ?: session("url.intended");

            if ($intendedUrl) {
                return redirect($intendedUrl);
            }

            // Default redirect to dashboard
            return redirect()->intended(route("dashboard"));
        }

        throw ValidationException::withMessages([
            "email" => "Email atau password tidak valid.",
        ]);
    }

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view("auth.register");
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
            "phone" => "required|string|max:20",
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
        ]);

        Auth::login($user);

        // Check for intended URL
        $intendedUrl = session("url.intended");

        if ($intendedUrl) {
            session()->forget("url.intended");
            return redirect($intendedUrl)->with(
                "success",
                "Registrasi berhasil! Selamat datang di Gravenue.",
            );
        }

        return redirect()
            ->route("dashboard")
            ->with(
                "success",
                "Registrasi berhasil! Selamat datang di Gravenue.",
            );
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home")->with("success", "Anda telah logout.");
    }

    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's bookings with facility and payment info
        $bookings = $user
            ->bookings()
            ->with(["facility", "payment"])
            ->orderBy("created_at", "desc")
            ->take(5)
            ->get();

        // Get booking statistics for the user
        $stats = [
            "total_bookings" => $user->bookings()->count(),
            "pending_bookings" => $user
                ->bookings()
                ->where("status", "pending")
                ->count(),
            "approved_bookings" => $user
                ->bookings()
                ->where("status", "approved")
                ->count(),
            "completed_bookings" => $user
                ->bookings()
                ->where("status", "completed")
                ->count(),
            "total_spent" => $user
                ->bookings()
                ->where("status", "approved")
                ->sum("total_price"),
        ];

        return view("auth.dashboard", compact("user", "bookings", "stats"));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        return view("auth.profile");
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" =>
                "required|string|email|max:255|unique:users,email," .
                Auth::id(),
            "phone" => "required|string|max:20",
        ]);

        $user = Auth::user();
        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
        ]);

        return redirect()
            ->back()
            ->with("success", "Profile berhasil diperbarui.");
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            "current_password" => "required",
            "password" => "required|string|min:8|confirmed",
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                "current_password" => "Password saat ini tidak valid.",
            ]);
        }

        Auth::user()->update([
            "password" => Hash::make($request->password),
        ]);

        return redirect()->back()->with("success", "Password berhasil diubah.");
    }
}
