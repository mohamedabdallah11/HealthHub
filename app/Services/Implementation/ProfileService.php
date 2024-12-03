<?php

namespace App\Services\Implementation;
use App\Services\Abstract\ProfileServiceInterface;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\ProfileResource;

class ProfileService implements ProfileServiceInterface
{
    public function showProfile(User $user)
    {
        if (!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }
        return ApiResponse::sendResponse(200, 'User profile fetched successfully', new ProfileResource($user));
    }

    public function updateProfile(ProfileUpdateRequest $request, User $user)
    {
        if (!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }

        $user->update($request->validated());

        if ($user->role == 'doctor') {
            $user->doctor->update($request->only(['bio', 'experience_year', 'fees']));
        } elseif ($user->role == 'client') {
            $user->client->update($request->only(['notes', 'medical_history']));
        }

        return ApiResponse::sendResponse(200, 'Profile updated successfully', new ProfileResource($user));
    }
}