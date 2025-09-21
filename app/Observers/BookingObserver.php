<?php

namespace App\Observers;

use App\Models\Booking;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        Log::info("New booking created: {$booking->id}");
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Cek apakah status berubah
        if ($booking->isDirty('status')) {
            $oldStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;
            
            Log::info("Booking {$booking->id} status changed from {$oldStatus} to {$newStatus}");
            
            // Kirim notifikasi WhatsApp jika status berubah ke approved atau rejected
            if (in_array($newStatus, ['approved', 'rejected'])) {
                // Dispatch job untuk mengirim notifikasi WhatsApp TANPA DELAY untuk respon cepat
                SendWhatsAppNotification::dispatch($booking->id, $newStatus);
                
                Log::info("WhatsApp notification job dispatched for booking {$booking->id} with status {$newStatus}");
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        Log::info("Booking deleted: {$booking->id}");
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        Log::info("Booking restored: {$booking->id}");
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        Log::info("Booking force deleted: {$booking->id}");
    }
}
