<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class socialiteAuthenticationController extends Controller
{  public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $findUser = User::where('provider_id', $user->id)->first();

            if ($findUser) {
                $data = new UserResource($findUser);
                $data['token'] = $findUser->createToken('authToken')->plainTextToken;
                return ApiResponse::sendResponse(200, 'User Logged In successfully', $data);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'client',
                    'provider_id' => $user->id,
                    'provider_type' => 'google',
                    'password' => Hash::make('my-google'),
                ]);
                $data = new UserResource($newUser);
                $data['token'] = $newUser->createToken('authToken')->plainTextToken;
                return ApiResponse::sendResponse(200, 'User Registered successfully', $data);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}