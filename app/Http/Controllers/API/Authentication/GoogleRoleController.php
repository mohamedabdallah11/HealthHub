<?php

namespace App\Http\Controllers\API\Authentication;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleRoleRequest;
use App\Http\Resources\CustomResource;
use App\Models\Doctor;
use App\Models\Client;
use Auth;
use Illuminate\Http\Request;

class GoogleRoleController extends Controller
{
 
    public function __invoke(GoogleRoleRequest $request)
    {   
        
        $user = Auth::user();
        

            $user->update(
            [
                'role' => $request->role,
                'phone' => $request->phone,
                'governorate' => $request->governorate,
                'gender' => $request->gender,
                'age' => $request->age
            ]
            );
            if($request->role == 'doctor'){
                Doctor::create([
                    'user_id' => $user->id
                ]);
            }
            if($request->role == 'client'){
                Client::create([
                    'user_id' => $user->id
                ]);
            }
        
        return ApiResponse::sendResponse(200, 'Role Added successfully', new CustomResource($user));
    }
}
