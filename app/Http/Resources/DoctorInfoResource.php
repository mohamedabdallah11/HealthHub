<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
         [    
        'id' => $this->id,
        'name' => $this->user->name, 
        'email' => $this->user->email,
        'phone' => $this->user->phone,
        'age' => $this->user->age,  
        'gender' => $this->user->gender,
        'specialization' => $this->specialties->pluck('name') ?? null, 
        'governorate' => $this->user->governorate,
        'address' => $this->user->address,
        'appointments' => $this->appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'date' => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'is_available' => $appointment->is_available,
            ];
        }),
        ];
    }
}
