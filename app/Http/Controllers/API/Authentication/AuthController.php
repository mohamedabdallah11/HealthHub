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
            'role' => $request->role?? 'client',
            'phone' => $request->phone,
            'governorate' => $request->governorate, 
            'gender' => $request->gender,
            'age' => $request->age
            
        ]);

        if($user->role == 'doctor'){
            Doctor::create([
                'user_id' => $user->id
            ]);
        }
        if($user->role == 'client'){
            Client::create([
                'user_id' => $user->id
            ]);
        }
 
    
        
        $data=new UserResource($user);
        $data['token']=$user->createToken('authToken')->plainTextToken;
        return ApiResponse::sendResponse(201, 'User created successfully', data: $data);
    }
    public function login(LoginRequest $request)    
    {   
        $credentials = [
            filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) 
            ? 'email' : 'phone' => $request->email_or_phone,
            'password' => $request->password,
        ];

       if (Auth::attempt($credentials) ) {

        
        /** @var \App\Models\User $user **/

        $user = Auth::user();
        $data=new UserResource($user);
        $data['token']=$user->createToken('authToken')->plainTextToken;

        return ApiResponse::sendResponse(200, 'User Logged In successfully', $data);
    } else {
        return ApiResponse::sendResponse(401, 'Invalid credentials', []);
    }

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse(200, 'User Logged out Successfully', []);
    }
 
}