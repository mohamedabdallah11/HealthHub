<?php

namespace App\Services\Implementation;
use App\Services\Abstract\ProfileServiceInterface;
use Illuminate\Support\Facades\Storage;

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

        $validatedData = $request->validated();

        $user->update($validatedData);

        if ($user->role === 'doctor') {
            $doctorData = $request->only([
                'bio',
                'experience_year',
                'fees',
                'clinicaddress',
                'clinicgovernate',
                'clinicname'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/doctor_images', $imageName);
                $doctorData['image'] = 'storage/doctor_images/' . $imageName;

                if ($user->doctor->image) {
                    Storage::delete(str_replace('storage/', 'public/', $user->doctor->image));
                }
            }

            $user->doctor->update($doctorData);

            $specialty = Specialty::where('name', $request->specialty)->first();
            if ($specialty) {
                Doctor_Specialty::updateOrCreate([
                    'doctor_id' => $user->doctor->id,
                    'specialty_id' => $specialty->id,
                ]);
            }

        } elseif ($user->role === 'client') {
            $user->client->update($request->only([
                'notes',
                'medical_history',
                'blood_type',
                'weight',
                'height'
            ]));
        }

        return ApiResponse::sendResponse(200, 'Profile updated successfully', new ProfileResource($user));
    }
}