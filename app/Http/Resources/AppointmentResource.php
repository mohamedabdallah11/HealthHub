<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'start_time' => Carbon::parse($this->start_time)->format('H:i'), 
            'end_time' => Carbon::parse($this->end_time)->format('H:i'), 
            'session_duration' => $this->session_duration,
            'is_available' => $this->is_available,
            'max_patients' => $this->max_patients
        ];
    }
}
