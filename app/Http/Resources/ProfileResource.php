<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'gender' => $this->gender,
            'slug' => $this->slug,
            'role' => $this->role,
            'phone' => $this->phone,
            'user_id' => $this->id
            
        ];

        if ($this->role == 'doctor') {
            $data['bio'] = $this->doctor->bio;
            $data['experience_year'] = $this->doctor->experience_year;
            $data['fees'] = $this->doctor->fees;
            $data['clinicgovernate'] = $this->doctor->clinicgovernate;
            $data['clinicaddress'] = $this->doctor->clinicaddress;
            $data['clinicname'] = $this->doctor->clinicname;
            $data['specialties'] = $this->doctor->specialties->pluck('name');
            $data['doctor_id'] = $this->doctor->id;
        }

        if ($this->role == 'client') {
            $data['client_id'] = $this->client->id;
            $data['notes'] = $this->client->notes;
            $data['medical_history'] = $this->client->medical_history;
            $data['blood_type'] = $this->client->blood_type;
            $data['weight'] = $this->client->weight;
            $data['height'] = $this->client->height;

        }

        return $data;
      }
}
