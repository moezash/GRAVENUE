<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bookingId;
    protected $status;

    /**
     * Create a new job instance.
     */
    public function __construct($bookingId, $status)
    {
        $this->bookingId = $bookingId;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("🚀 Starting WhatsApp notification job for booking {$this->bookingId} with status: {$this->status}");
            
            $whatsappService = new WhatsAppService();
            
            if ($this->status === 'approved') {
                Log::info("📨 Sending APPROVED notification for booking {$this->bookingId}");
                $result = $whatsappService->sendBookingApprovedNotification($this->bookingId);
                Log::info("✅ WhatsApp APPROVED notification job completed for booking {$this->bookingId}: " . ($result ? 'success' : 'failed'));
            } elseif ($this->status === 'rejected') {
                Log::info("📨 Sending REJECTED notification for booking {$this->bookingId}");
                $result = $whatsappService->sendBookingRejectedNotification($this->bookingId);
                Log::info("❌ WhatsApp REJECTED notification job completed for booking {$this->bookingId}: " . ($result ? 'success' : 'failed'));
            } else {
                Log::warning("⚠️ Unknown status '{$this->status}' for booking {$this->bookingId}");
            }
            
        } catch (\Exception $e) {
            Log::error("💥 WhatsApp notification job failed for booking {$this->bookingId} with status {$this->status}: " . $e->getMessage());
            throw $e; // Re-throw untuk retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("WhatsApp notification job permanently failed for booking {$this->bookingId}: " . $exception->getMessage());
    }
}
