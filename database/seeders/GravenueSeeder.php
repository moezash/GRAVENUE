<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GravenueSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if not exists
        Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('password'),
                'email' => 'admin@smkn4malang.sch.id',
                'full_name' => 'Administrator'
            ]
        );

        // Create sample facilities
        $facilities = [
            [
                'name' => 'Aula Utama',
                'description' => 'Aula besar dengan kapasitas 500 orang, dilengkapi dengan sound system dan proyektor',
                'capacity' => 500,
                'price_per_hour' => 150000.00,
                'category' => 'Event Space',
                'features' => 'Sound System, Proyektor, AC, Panggung',
                'status' => 'available'
            ],
            [
                'name' => 'Laboratorium Komputer 1',
                'description' => 'Lab komputer dengan 40 unit PC terbaru dan koneksi internet cepat',
                'capacity' => 40,
                'price_per_hour' => 50000.00,
                'category' => 'Classroom',
                'features' => '40 PC, Internet, AC, Proyektor',
                'status' => 'available'
            ],
            [
                'name' => 'Lapangan Basket',
                'description' => 'Lapangan basket outdoor dengan standar internasional',
                'capacity' => 100,
                'price_per_hour' => 30000.00,
                'category' => 'Sports',
                'features' => 'Ring Basket, Lampu Penerangan',
                'status' => 'available'
            ],
            [
                'name' => 'Ruang Serbaguna',
                'description' => 'Ruang fleksibel untuk berbagai kegiatan dengan kapasitas 100 orang',
                'capacity' => 100,
                'price_per_hour' => 75000.00,
                'category' => 'Event Space',
                'features' => 'Meja Kursi, Sound System, AC',
                'status' => 'available'
            ],
            [
                'name' => 'Laboratorium Multimedia',
                'description' => 'Lab multimedia dengan peralatan editing video dan audio profesional',
                'capacity' => 30,
                'price_per_hour' => 80000.00,
                'category' => 'Classroom',
                'features' => 'Editing Suite, Audio Equipment, Proyektor',
                'status' => 'available'
            ],
            [
                'name' => 'Auditorium',
                'description' => 'Auditorium modern dengan kapasitas 300 orang dan fasilitas lengkap',
                'capacity' => 300,
                'price_per_hour' => 200000.00,
                'category' => 'Event Space',
                'features' => 'Sound System, Lighting, AC, Panggung Besar',
                'status' => 'available'
            ],
            [
                'name' => 'Ruang Kelas A1',
                'description' => 'Ruang kelas standar dengan kapasitas 36 siswa',
                'capacity' => 36,
                'price_per_hour' => 20000.00,
                'category' => 'Classroom',
                'features' => 'Whiteboard, Proyektor, AC',
                'status' => 'available'
            ]
        ];

        foreach ($facilities as $facilityData) {
            Facility::create($facilityData);
        }

        // Create sample bookings
        $facilities = Facility::all();
        
        $bookings = [
            [
                'facility_id' => $facilities[0]->id, // Aula Utama
                'user_name' => 'John Doe',
                'user_email' => 'john@example.com',
                'user_phone' => '081234567890',
                'organization' => 'PT. Example',
                'event_name' => 'Seminar Teknologi',
                'booking_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(7),
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'participants' => 50,
                'total_price' => 1500000.00,
                'additional_notes' => 'Perlu sound system tambahan',
                'status' => 'approved'
            ],
            [
                'facility_id' => $facilities[1]->id, // Lab Komputer 1
                'user_name' => 'Jane Smith',
                'user_email' => 'jane@example.com',
                'user_phone' => '081234567891',
                'organization' => 'Universitas Malang',
                'event_name' => 'Pelatihan Programming',
                'booking_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(12),
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'participants' => 30,
                'total_price' => 1500000.00,
                'additional_notes' => 'Perlu akses internet stabil',
                'status' => 'pending'
            ],
            [
                'facility_id' => $facilities[2]->id, // Lapangan Basket
                'user_name' => 'Mike Johnson',
                'user_email' => 'mike@example.com',
                'user_phone' => '081234567892',
                'organization' => 'Komunitas Basket Malang',
                'event_name' => 'Turnamen Basket',
                'booking_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(14),
                'start_time' => '07:00:00',
                'end_time' => '18:00:00',
                'participants' => 20,
                'total_price' => 300000.00,
                'additional_notes' => 'Perlu lampu tambahan untuk malam',
                'status' => 'approved'
            ]
        ];

        foreach ($bookings as $bookingData) {
            $booking = Booking::create($bookingData);
            
            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_amount' => $booking->total_price,
                'payment_status' => $booking->status === 'approved' ? 'paid_dummy' : 'pending'
            ]);
        }

        // Create sample schedules for next 30 days
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(30);
        
        foreach ($facilities as $facility) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                Schedule::create([
                    'facility_id' => $facility->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => 'available'
                ]);
                
                $currentDate->addDay();
            }
        }

        // Update schedules for booked dates
        $bookings = Booking::where('status', 'approved')->get();
        foreach ($bookings as $booking) {
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
    }
}