<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientBookingInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slot_start_time||session_start_time' => $this->slot_start_time,
            'slot_end_time||session_end_time' => $this->slot_end_time,

            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'doctor_id' => $this->doctor_id,
            'client_id' => $this->user_id,
            'status' => $this->status,
            'booking_time' => Carbon::parse($this->created_at)->format('yy-M-d H:i:s'),
            'appointment' => [
                'start_time' => $this->appointment->start_time,
                'end_time' => $this->appointment->end_time,
                'date' => $this->appointment->date,
            ],
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->user->name,
                'specialties' => $this->doctor->specialties->pluck('name'),
            ],
            'client' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'age' => $this->user->age,
                'gender' => $this->user->gender,
            ],
        ];  
            }
}
