<?php

namespace App\Http\Controllers\Api\Authentication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Mail\OtpMail;
class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {   
        $user = User::where('email', $request->email)->first();
        if ($user && $user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 422);
        }
        $request->validate([
            'email' => 'required|email|exists:users,email', 
        ]);

        $otp = rand(100000, 999999);

        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);
    
        $cachedOtp = Cache::get('otp_' . $request->email);
    
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }
    
        Cache::forget('otp_' . $request->email);
    
        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => 'Email verified successfully'],200);
    }
    
    }
