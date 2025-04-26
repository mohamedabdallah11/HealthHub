<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'doctor_id' => $this->doctor_id,
            'client_id' => $this->user_id,
            'status' => $this->status,
            'slot_start_time' =>Carbon::parse($this->slot_start_time)->format('H:i'),
            'slot_end_time' =>Carbon::parse($this->slot_end_time)->format('H:i'),
            'appointment' => [
                'start_time' => $this->appointment->start_time,
                'end_time' => $this->appointment->end_time,
                'capacity' => $this->appointment->max_patients,
                'available_capacity' => $this->appointment->max_patients - $this->appointment->bookings->count(),
            ],
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->user->name,
                'specialties' => $this->doctor->specialties->pluck('name'),
            ],
   
        ];    }
}
