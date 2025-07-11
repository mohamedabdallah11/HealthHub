<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Client;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function register(RegisterRequest  $request)
    {
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {

            return ApiResponse::sendResponse(409, 'User already exists', []);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'client',
            'phone' => $request->phone,
            'governorate' => $request->governorate,
            'gender' => $request->gender,
            'age' => $request->age

        ]);

        if ($user->role == 'doctor') {
            Doctor::create([
                'user_id' => $user->id
            ]);
        }
        if ($user->role == 'client') {
            Client::create([
                'user_id' => $user->id
            ]);
        }



        $data = new UserResource($user);
        $data['token'] = $user->createToken('authToken')->plainTextToken;
        return ApiResponse::sendResponse(201, 'User created successfully', data: $data);
    }
    public function login(LoginRequest $request)
    {
        $credentials = [
            filter_var($request->email, FILTER_VALIDATE_EMAIL)
                ? 'email' : 'phone' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {


            /** @var \App\Models\User $user **/

            $user = Auth::user();
            $data = new UserResource($user);
            $data['token'] = $user->createToken('authToken')->plainTextToken;

            return ApiResponse::sendResponse(200, 'User Logged In successfully', $data);
        } else {
            return ApiResponse::sendResponse(401, 'Invalid credentials', []);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token && $token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }

        return ApiResponse::sendResponse(200, 'User Logged out Successfully', []);
    }
    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::sendResponse(404, ['message' => 'User not found'], []);
        }

        if (!$user->email_verified_at) {
            return ApiResponse::sendResponse(403, ['message' => 'Email is not verified'], []);
        }

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        Mail::to($request->email)->send(new OtpMail($otp));

        return ApiResponse::sendResponse(200, 'OTP sent successfully', []);
    }

    public function verifyOtpAndResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return ApiResponse::sendResponse(400, 'Invalid or expired OTP', []);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('otp_' . $request->email);

        return ApiResponse::sendResponse(200, 'Password reset successfully', []);
    }

    public function deleteUser(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::sendResponse(401, 'Unauthorized', []);
        }

        $user->delete();

        return ApiResponse::sendResponse(200, 'Your account has been deleted successfully.', []);
    }
}
