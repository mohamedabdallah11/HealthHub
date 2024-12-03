<?php

namespace App\Http\Controllers\Api\Profile;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\Abstract\ProfileServiceInterface;
use App\Services\Implementation\ProfileService;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    protected  $profileService;
    public function __construct(ProfileServiceInterface $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show()
    {
    /** 
   * @var User $user
   */
  
    $user = Auth::user();
    return $this->profileService->showProfile($user);


}
    public function update(ProfileUpdateRequest $request)
    {   
        
        /**
       * @var User $user
       */
      
        $user = Auth::user();
        return $this->profileService->updateProfile($request, $user);

}

    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();  

        if (!Hash::check($request->oldPassword, $user->password)) 
            return ApiResponse::sendResponse(400, 'Old password is incorrect', []); 
        $user->update([
            'password' => Hash::make($request->newPassword),
        ]);
        return ApiResponse::sendResponse(200, 'Password updated successfully', []);
    }

}
