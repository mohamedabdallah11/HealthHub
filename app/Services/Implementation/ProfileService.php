<?php

namespace App\Services\Implementation;
use App\Services\Abstract\ProfileServiceInterface;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\ProfileResource;

use App\Models\Specialty;

use App\Models\Doctor_Specialty;

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
            $user->doctor->update($request->only(
                ['bio', 'experience_year', 'fees', 'clinicaddress', 'clinicgovernate',
                        'clinicname']));
            $specialty = Specialty::where('name', $request->specialty)->first();
            if($specialty){
                Doctor_Specialty::updateOrCreate(['doctor_id' => $user->doctor->id,'specialty_id' => $specialty->id]);
            }
        } elseif ($user->role == 'client') {
            $user->client->update($request->only(['notes', 'medical_history','blood_type','weight','height']));
        }

        return ApiResponse::sendResponse(200, 'Profile updated successfully', new ProfileResource($user));
    }
}