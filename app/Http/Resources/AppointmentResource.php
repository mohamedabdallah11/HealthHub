<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'doctor_name' => $this->doctor->user->name, 
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'session_duration' => $this->session_duration,
            'is_available' => $this->is_available,
            "max_patients"=> $this->max_patients
        ];
    }
}
