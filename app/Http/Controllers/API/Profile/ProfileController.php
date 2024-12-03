<?php

namespace App\Http\Controllers\Api\Profile;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function show()
    {
        $user= Auth::user();
        if(!$user)
        {
            return ApiResponse::sendResponse(404,'User not found',[]);
        }
        return ApiResponse::sendResponse(200,'User profile fetched successfully',new ProfileResource($user));
    }
    public function update(ProfileUpdateRequest $request)
    {


         $user = Auth::user(); 
         
        if (!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }
   /**
 * @var \App\Models\User $user
 */
        $user->update($request->validated());

  

        if ($user->role == 'doctor') {
                    $user->doctor->update($request->only(['bio', 'experience_year', 'fees']));
                } elseif ($user->role == 'client') {
                    $user->client->update($request->only(['notes', 'medical_history'])); 
                }
                return ApiResponse::sendResponse(200,'Profile updated successfully',new ProfileResource($user));
 


}

}
