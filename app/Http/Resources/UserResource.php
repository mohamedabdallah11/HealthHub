<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'token'=>$this->token,
            'name'=>$this->name,
            'email'=>$this->email,
            'role'=>$this->role,
            'slug'=>$this->slug,
            'email_verified_at'=>$this->email_verified_at ? $this->email_verified_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
