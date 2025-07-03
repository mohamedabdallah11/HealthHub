<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class socialiteAuthenticationController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        if ($request->has('base_url')) {
            session(['oauth_base_url' => $request->base_url]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $httpClient = new Client(['verify' => false]);

            /** @var \Laravel\Socialite\Two\GoogleProvider $provider */
            $provider = Socialite::driver('google');
            $provider->setHttpClient($httpClient);
            $user = $provider->stateless()->user();

            $findUser = User::where('provider_id', $user->id)->first();

            if ($findUser) {
                $authUser = $findUser;
            } else {
                $authUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider_id' => $user->id,
                    'provider_type' => 'google',
                    'password' => Hash::make('my-google'),
                    'role' => 'deactivated',
                    'email_verified_at' => now(), 
                ]);
            }

            $token = $authUser->createToken('authToken')->plainTextToken;

            $baseUrl = session('oauth_base_url');

            session()->forget('oauth_base_url');

            $frontendUrl = rtrim($baseUrl, '/') . '/api/auth/google/callback';

            return redirect()->to("{$frontendUrl}?token={$token}&slug={$authUser->slug}&role={$authUser->role}");

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function handleGoogleAccessToken(Request $request)
    {
        try {
            $request->validate([
                'access_token' => 'required|string',
            ]);

            $httpClient = new Client(['verify' => false]);

            $user = Socialite::driver('google')
                ->setHttpClient($httpClient)
                ->stateless()
                ->userFromToken($request->access_token);

            return $this->loginOrRegisterGoogleUser($user);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function loginOrRegisterGoogleUser($googleUser)
    {
        $findUser = User::where('provider_id', $googleUser->id)->first();

        if ($findUser) {
            $data = new UserResource($findUser);
            $data['token'] = $findUser->createToken('authToken')->plainTextToken;
            return ApiResponse::sendResponse(200, 'User Logged In successfully', $data);
        } else {
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'provider_id' => $googleUser->id,
                'provider_type' => 'google',
                'password' => Hash::make('my-google'),
                'role' => 'deactivated',
            ]);
            $data = new UserResource($newUser);
            $data['token'] = $newUser->createToken('authToken')->plainTextToken;
            return ApiResponse::sendResponse(200, 'User Registered successfully', $data);
        }
    }
}