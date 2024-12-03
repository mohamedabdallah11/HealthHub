<?php

namespace App\Http\Controllers\Api\Authentication;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
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
           /*  'phone' => $this->phone,
            'governorate' => $this->governorate, */
            
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
 
    

      /*   $data['token'] = $user->createToken('authToken')->plainTextToken;
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['role'] = $user->role; */
        
        $data=new UserResource($user);
        $data['token']=$user->createToken('authToken')->plainTextToken;
        return ApiResponse::sendResponse(201, 'User created successfully', $data);
    }
    public function login(LoginRequest $request)
    {

       if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        
      /*   
        $data['token'] = $user->createToken('authToken')->plainTextToken;
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['role'] = $user->role; */
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