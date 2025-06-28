<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorBookingMangmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>

     */
     protected $status;

    public function __construct($resource, $status = 'confirmed')
    {
        parent::__construct($resource);
        $this->status = $status;
    }
    public function toArray(Request $request): array
    {
        return [
            'appointment_id' => $this->appointment_id,
            'status' => $this->status,
    
            'slots' => $this->appointment->bookings
                ->where('status', $this->status)
                ->groupBy('slot_start_time')
                ->map(function ($bookings, $slotTime) {
                    return [
                        'booking_id'=> $bookings->first()->id,
                        'slot_start_time' => $slotTime,
                        'slot_end_time' => $bookings->first()->slot_end_time, 
                        'google_meet_link' => $bookings->first()->google_meet_link,
                        'clients' => $bookings->map(function ($booking) {
                            return [
                                'name' => $booking->user->name,
                                'email' => $booking->user->email,
                                'phone' => $booking->user->phone,
                            ];
                        })->values(),
                    ];
                })->values(),
    
            'appointment' => [
                'start_time' => $this->appointment->start_time,
                'end_time' => $this->appointment->end_time,
                'capacity' => $this->appointment->max_patients,
                'available_capacity' => $this->appointment->max_patients - $this->appointment->bookings->where('status', 'confirmed')->count(),
            ],
    
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->user->name,
                'specialties' => $this->doctor->specialties->pluck('name'),
            ],
        ];
    }
    
    
}
