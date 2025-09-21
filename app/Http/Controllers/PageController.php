<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

class PageController extends Controller
{
    public function home()
    {
        return view("home");
    }

    public function about()
    {
        return view("about");
    }

    public function contact()
    {
        return view("contact");
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'unread'
        ]);

        return redirect()->route('contact')->with('success', 'Pesan Anda telah berhasil dikirim. Kami akan segera merespons.');
    }

    public function schedule()
    {
        $facilities = Facility::available()->get();
        return view("schedule", compact("facilities"));
    }

    public function facilities(Request $request)
    {
        $query = Facility::available();

        if ($request->has("category") && $request->category !== "all") {
            $query->byCategory($request->category);
        }

        $facilities = $query->orderBy("category")->orderBy("name")->get();

        return view("facilities", compact("facilities"));
    }

    public function facility($id)
    {
        $facility = Facility::available()->findOrFail($id);
        return view("facility", compact("facility"));
    }

    public function bookingForm(Request $request, $facilityId)
    {
        $facility = Facility::available()->findOrFail($facilityId);
        return view("booking", compact("facility"));
    }

    public function submitBooking(Request $request)
    {
        $request->validate([
            "facility_id" => "required|integer|exists:facilities,id",
            "user_name" => "required|string|max:100",
            "user_email" => "required|email|max:100",
            "user_phone" => "required|string|max:20",
            "organization" => "nullable|string|max:100",
            "event_name" => "required|string|max:150",
            "booking_date" => "required|date|after_or_equal:today",
            "end_date" => "nullable|date|after_or_equal:booking_date",
            "start_time" => "nullable|string",
            "end_time" => "nullable|string",
            "participants" => "required|integer|min:1",
            "additional_notes" => "nullable|string",
        ]);

        $facility = Facility::findOrFail($request->facility_id);

        // Calculate price based on time range
        $totalPrice = $facility->price_per_hour; // Default to 1 hour
        
        if ($request->start_time && $request->end_time) {
            $startTime = Carbon::createFromFormat('H:i', $request->start_time);
            $endTime = Carbon::createFromFormat('H:i', $request->end_time);
            
            if ($endTime > $startTime) {
                $hours = ceil($startTime->diffInMinutes($endTime) / 60);
                $totalPrice = $facility->price_per_hour * $hours;
            }
        }

        $user = Auth::user();

        $booking = Booking::create([
            "facility_id" => $request->facility_id,
            "user_id" => $user->id,
            "user_name" => $request->user_name,
            "user_email" => $request->user_email,
            "user_phone" => $request->user_phone,
            "organization" => $request->organization,
            "event_name" => $request->event_name,
            "booking_date" => $request->booking_date,
            "end_date" => $request->end_date,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "participants" => $request->participants,
            "total_price" => $totalPrice,
            "additional_notes" => $request->additional_notes,
            "status" => "pending",
        ]);

        // Create payment record (matches database structure)
        Payment::create([
            "booking_id" => $booking->id,
            "payment_amount" => $totalPrice,
            "payment_status" => "pending",
        ]);

        return redirect()
            ->route("booking.status", $booking->id)
            ->with("success", "Pengajuan sewa berhasil dikirim!");
    }

    public function bookingStatus($id)
    {
        $booking = Booking::with(["facility", "payment"])->findOrFail($id);
        return view("booking-status", compact("booking"));
    }

    public function payment($id)
    {
        $booking = Booking::with(["facility", "payment"])->findOrFail($id);

        if ($booking->status !== "approved") {
            return redirect()
                ->route("booking.status", $id)
                ->with("error", "Booking belum disetujui untuk pembayaran.");
        }

        // Set Midtrans Configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        // Prepare transaction details
        $orderId = 'GRAVENUE-' . $booking->id . '-' . time();
        
        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => (int) $booking->total_price,
        ];

        $itemDetails = [
            [
                'id' => 'facility-' . $booking->facility_id,
                'price' => (int) $booking->total_price,
                'quantity' => 1,
                'name' => 'Sewa Fasilitas: ' . $booking->facility->name,
            ]
        ];

        $customerDetails = [
            'first_name' => $booking->user_name,
            'email' => $booking->user_email,
            'phone' => $booking->user_phone,
        ];

        $transaction = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transaction);
            
            // Update payment record with Midtrans data
            $booking->payment->update([
                "transaction_id" => $orderId,
                "payment_status" => "pending",
                "payment_date" => null,
                "notes" => "Midtrans payment initiated",
            ]);

            return view('payment', compact('booking', 'snapToken'));
            
        } catch (Exception $e) {
            return redirect()
                ->route("booking.status", $id)
                ->with("error", "Gagal membuat pembayaran: " . $e->getMessage());
        }
    }

    public function processPayment(Request $request, $id)
    {
        $booking = Booking::with("payment")->findOrFail($id);

        if ($booking->status !== "approved") {
            return redirect()
                ->route("booking.status", $id)
                ->with("error", "Booking belum disetujui untuk pembayaran.");
        }

        // Set Midtrans Configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        // Prepare transaction details
        $orderId = 'GRAVENUE-' . $booking->id . '-' . time();
        
        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => (int) $booking->total_price,
        ];

        $itemDetails = [
            [
                'id' => 'facility-' . $booking->facility_id,
                'price' => (int) $booking->total_price,
                'quantity' => 1,
                'name' => 'Sewa Fasilitas: ' . $booking->facility->name,
            ]
        ];

        $customerDetails = [
            'first_name' => $booking->user_name,
            'email' => $booking->user_email,
            'phone' => $booking->user_phone,
        ];

        $transaction = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transaction);
            
            // Update payment record with Midtrans data
            $booking->payment->update([
                "transaction_id" => $orderId,
                "payment_status" => "pending",
                "payment_date" => null,
                "notes" => "Midtrans payment initiated",
            ]);

            return view('payment', compact('booking', 'snapToken'));
            
        } catch (Exception $e) {
            return redirect()
                ->route("booking.status", $id)
                ->with("error", "Gagal membuat pembayaran: " . $e->getMessage());
        }
    }

    public function paymentNotification(Request $request)
    {
        // Set Midtrans Configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        try {
            $notification = new \Midtrans\Notification();
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            // Find payment by transaction ID
            $payment = Payment::where('transaction_id', $orderId)->first();

            if (!$payment) {
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // Handle different transaction statuses
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Challenge fraud
                    $payment->update([
                        'payment_status' => 'challenge',
                        'payment_date' => now(),
                    ]);
                } else if ($fraudStatus == 'accept') {
                    // Success
                    $payment->update([
                        'payment_status' => 'paid',
                        'payment_date' => now(),
                    ]);
                    
                    // Update booking status to completed
                    $payment->booking->update(['status' => 'completed']);
                }
            } else if ($transactionStatus == 'settlement') {
                // Success
                $payment->update([
                    'payment_status' => 'paid',
                    'payment_date' => now(),
                ]);
                
                // Update booking status to completed
                $payment->booking->update(['status' => 'completed']);
            } else if ($transactionStatus == 'pending') {
                // Pending
                $payment->update([
                    'payment_status' => 'pending',
                ]);
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                // Failed
                $payment->update([
                    'payment_status' => 'failed',
                ]);
            }

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
