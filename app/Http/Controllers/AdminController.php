<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Admin;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware akan diatur di routes/web.php
    }

    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::pending()->count(),
            'approved_bookings' => Booking::approved()->count(),
            'total_facilities' => Facility::count(),
            'total_revenue' => Payment::paid()->sum('payment_amount'),
            'recent_bookings' => Booking::with('facility')->latest()->take(5)->get()
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function facilities()
    {
        $facilities = Facility::orderBy('name')->get();
        return view('admin.facilities', compact('facilities'));
    }

    public function createFacility()
    {
        return view('admin.facility-form');
    }

    public function storeFacility(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'price_per_hour' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'features' => 'nullable|string',
            'status' => 'required|in:available,maintenance,unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        Facility::create($data);

        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function editFacility($id)
    {
        $facility = Facility::findOrFail($id);
        return view('admin.facility-form', compact('facility'));
    }

    public function updateFacility(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'price_per_hour' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'features' => 'nullable|string',
            'status' => 'required|in:available,maintenance,unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facility->update($data);

        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function deleteFacility($id)
    {
        $facility = Facility::findOrFail($id);
        
        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }
        
        $facility->delete();

        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil dihapus.');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with('facility');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10);

        return view('admin.bookings', compact('bookings'));
    }

    public function bookingDetail($id)
    {
        $booking = Booking::with(['facility', 'payment'])->findOrFail($id);
        return view('admin.booking-detail', compact('booking'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled'
        ]);

        $oldStatus = $booking->status;
        $newStatus = $request->status;

        // Update booking status (this will trigger Observer)
        $booking->update(['status' => $newStatus]);

        // Update schedules if approved
        if ($newStatus === 'approved') {
            $startDate = Carbon::parse($booking->booking_date);
            $endDate = $booking->end_date ? Carbon::parse($booking->end_date) : $startDate;
            
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                Schedule::where('facility_id', $booking->facility_id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->update([
                        'booking_id' => $booking->id,
                        'status' => 'booked'
                    ]);
                
                $currentDate->addDay();
            }
        }

        // 🚀 AUTO-PROCESS NOTIFICATIONS IMMEDIATELY
        if (in_array($newStatus, ['approved', 'rejected'])) {
            try {
                // Process any pending notification jobs immediately
                \Illuminate\Support\Facades\Artisan::call('queue:work', [
                    '--once' => true,
                    '--quiet' => true
                ]);
                
                \Illuminate\Support\Facades\Log::info("🚀 Auto-processed notification for booking {$id} status change: {$oldStatus} → {$newStatus}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("❌ Failed to auto-process notification: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.bookings')->with('success', 'Status booking berhasil diperbarui dan notifikasi WhatsApp telah dikirim.');
    }

    public function payments()
    {
        $payments = Payment::with('booking.facility')->latest()->paginate(10);
        return view('admin.payments', compact('payments'));
    }

    public function schedule(Request $request)
    {
        // Set timezone to Indonesia
        $now = now()->setTimezone('Asia/Jakarta');
        
        $facilities = Facility::all();
        $selectedFacility = $request->get('facility_id');
        $selectedMonth = $request->get('month', $now->format('Y-m'));
        
        // Parse month to get start and end dates
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $query = Schedule::with(['facility', 'booking']);

        if ($selectedFacility) {
            $query->where('facility_id', $selectedFacility);
        }

        $schedules = $query->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return view('admin.schedule', compact('facilities', 'schedules', 'selectedFacility', 'selectedMonth'));
    }

    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $bookings = Booking::with('facility')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->get();

        $revenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->paid()
            ->sum('payment_amount');

        $facilityUsage = $bookings->groupBy('facility_id')
            ->map(function ($facilityBookings) {
                return [
                    'facility' => $facilityBookings->first()->facility,
                    'count' => $facilityBookings->count(),
                    'revenue' => $facilityBookings->sum('total_price')
                ];
            })
            ->sortByDesc('count');

        return view('admin.reports', compact('bookings', 'revenue', 'facilityUsage', 'startDate', 'endDate'));
    }

    public function messages()
    {
        $messages = Contact::orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = Contact::unread()->count();
        
        return view('admin.messages', compact('messages', 'unreadCount'));
    }

    public function messageDetail($id)
    {
        $message = Contact::findOrFail($id);
        
        // Mark as read if unread
        if ($message->status === 'unread') {
            $message->markAsRead();
        }
        
        return view('admin.message-detail', compact('message'));
    }

    public function replyMessage(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:2000'
        ]);

        $message = Contact::findOrFail($id);
        $message->markAsReplied($request->reply);

        return redirect()->route('admin.messages.detail', $id)
            ->with('success', 'Balasan berhasil dikirim.');
    }

    public function markMessageRead($id)
    {
        $message = Contact::findOrFail($id);
        $message->markAsRead();

        return redirect()->route('admin.messages')
            ->with('success', 'Pesan ditandai sebagai sudah dibaca.');
    }
}