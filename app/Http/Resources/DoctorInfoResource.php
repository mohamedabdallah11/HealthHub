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
        'role_activation' => $this->role_activation,
        'slug' => $this->user->slug,
        'email_verified_at' => $this->user->email_verified_at ? $this->user->email_verified_at->format('Y-m-d H:i:s') : null,
        'fees' => $this->fees,
        'bio' => $this->bio,
        'image' => $this->image ? asset($this->image) : null,
        'clinicaddress' => $this->clinicaddress,
        'clinicgovernate' => $this->clinicgovernate,
        'clinicname' => $this->clinicname,
        'name' => $this->user->name, 
        'role' => $this->user->role,
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
                'session_duration' => $appointment->session_duration,
                'max_patients' => $appointment->max_patients,
                'end_time' => $appointment->end_time,
                'is_available' => $appointment->is_available,
            ];
        }),
    ];
    }
}  
