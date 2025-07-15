<?php

namespace App\Http\Controllers\API\AdminDashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;

class RoleActivationController extends Controller
{
     public function update(Request $request, $id)
    {
        $request->validate([
            'role_activation' => 'required|in:true,false',
        ]);

        $doctor = Doctor::find($id);

        if (!$doctor) {
            return ApiResponse::sendResponse(404, 'Doctor not found',['']);
        }

        $doctor->role_activation = $request->role_activation;
        $doctor->save();

        return ApiResponse::sendResponse(200, 'Role activation updated successfully', new DoctorResource($doctor));
    }
public function pending()
{
    $pendingDoctors = Doctor::where('role_activation', 'false')->get();

  return ApiResponse::sendResponse(200, 'Pending doctors retrieved successfully', DoctorResource::collection($pendingDoctors));
}
}
