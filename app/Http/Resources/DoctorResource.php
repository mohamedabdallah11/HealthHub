<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'slug' => $this->user->slug,
            'name' => $this->user->name, 
            'specialization' => $this->specialties->pluck('name') ?? null, 
            'experience_year'=>$this->experience_year

        ];   
     }
}
