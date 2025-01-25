<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\DoctorInfoResource;
use App\Models\Doctor;
use App\Helpers\ApiResponse;
use App\Http\Resources\DoctorResource;



class DoctorController extends Controller
{
    public function allDoctors()
    {
        try {

            $doctors = Doctor::with(['user', 'specialties'])->get();

            return ApiResponse::sendResponse(
                200,
                'Doctors list fetched successfully.',
                DoctorResource::collection($doctors)
            );
        } catch (\Exception $e) {
            return ApiResponse::sendResponse(500, 'Failed to fetch dashboard data.', $e->getMessage());
        }
    }
    public function doctorInformation($id)
    {

        if (!$doctor = Doctor::find($id)) {
            return ApiResponse::sendResponse(404, 'Doctor not found', []);
        }

        $doctor = Doctor::with(['user', 'appointments', 'specialties'])
            ->where('id', $id)->first();
        $data = new DoctorInfoResource($doctor);

        if ($doctor)
            return ApiResponse::sendResponse(
                200,
                'Doctors fetched successfully.',
                $data
            );


        return ApiResponse::sendResponse(500, 'Failed to fetch doctor data.', []);

    }
    public function filterBySpecialty(Request $request)
    {
        $specialtyId = $request->input('specialty_id');
    
        $doctors = Doctor::whereHas('specialties', function ($query) use ($specialtyId) {
            $query->where('specialties.id', $specialtyId);
        })->get();
    
        return ApiResponse::sendResponse(200, 'Doctors filtered by specialty successfully', DoctorInfoResource::collection($doctors));
    }
    public function searchByName(Request $request)
    {
        if ($request->has('name') && !empty($request->name)) {
            $doctors = Doctor::whereHas('user', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            })->with('specialties', 'appointments')->get();

            return ApiResponse::sendResponse(200, 'Doctors retrieved successfully', DoctorInfoResource::collection($doctors));
        }

        return ApiResponse::sendResponse(400, 'Name parameter is required', []);
    }
}

