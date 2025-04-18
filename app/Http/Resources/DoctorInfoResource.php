<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
         return [    
        'id' => $this->id,
        'fees' => $this->fees,
        'bio' => $this->bio,
        'name' => $this->user->name, 
        'email' => $this->user->email,
        'phone' => $this->user->phone,
        'age' => $this->user->age,  
        'gender' => $this->user->gender,
        'specialization' => $this->specialties->pluck('name') ?? null, 
        'appointments' => $this->appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'date' => $appointment->date,
                'day' => Carbon::parse($appointment->date)->format('l'), 
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'is_available' => $appointment->is_available,
            ];
        }),
    ];
    }
}
