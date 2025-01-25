<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Http\Resources\DoctorInfoResource;
use App\Helpers\ApiResponse;

class DoctorController extends Controller
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
        
            $doctor = Doctor::with(['user','appointments','specialties'])
            ->where('id', $id)->first();
            $data=new DoctorInfoResource($doctor);

        if($doctor)
            return ApiResponse::sendResponse(200, 'Doctors fetched successfully.',
            $data);


            return ApiResponse::sendResponse(500, 'Failed to fetch doctor data.', []);
        
    }
    public function filterationBySpeciality(){
        //
    }
    public function searchByName(){
        //
    }
}
