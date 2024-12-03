<?php

namespace App\Services\Abstract;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;

interface ProfileServiceInterface
{
    public function showProfile(User $user);

    public function updateProfile(ProfileUpdateRequest $request, User $user);
}