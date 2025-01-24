<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorInfoResource;
use App\Http\Resources\DoctorResource;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Helpers\ApiResponse;

class HomeController extends Controller
{
    public function allDoctors()
    {
        try {
            
            $doctors = Doctor::with(['user','specialties'])->get();

            return ApiResponse::sendResponse(200, 'Doctors list fetched successfully.',
              DoctorResource::collection($doctors));
        } catch (\Exception $e) {
            return ApiResponse::sendResponse(500, 'Failed to fetch dashboard data.', $e->getMessage());
        }
    }
    public function doctorInformation($id)
    {   
        if (!$doctor = Doctor::find($id)) {
            return ApiResponse::sendResponse(404, 'Doctor not found', []);
        }
        
        try {
            $doctor = Doctor::with(['user','appointments','specialties'])
            ->where('id', $id)->first();
            $data=new DoctorInfoResource($doctor);


            return ApiResponse::sendResponse(200, 'Doctors fetched successfully.',
            $data);


        } catch (\Exception $e) {
            return ApiResponse::sendResponse(500, 'Failed to fetch doctor data.', $e->getMessage());
        }
    }
}
