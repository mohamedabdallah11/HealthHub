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
            'role' => $this->role,
            'phone' => $this->phone,
            'governorate' => $this->governorate,
            'id' => $this->id
        ];

        if ($this->role == 'doctor') {
            $data['bio'] = $this->doctor->bio;
            $data['experience_year'] = $this->doctor->experience_year;
            $data['fees'] = $this->doctor->fees;
            $data['specialties'] = $this->doctor->specialties->pluck('name');
        }

        if ($this->role == 'client') {
            $data['notes'] = $this->client->notes;
            $data['medical_history'] = $this->client->medical_history;
        }

        return $data;
      }
}
