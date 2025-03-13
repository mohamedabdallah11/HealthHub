<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\DoctorInfoResource;
use App\Models\Doctor;
use App\Helpers\ApiResponse;
use App\Http\Resources\DoctorResource;



class DoctorController extends Controller
{public function allDoctors(Request $request)
    {
        try {
            $doctors = Doctor::with(['user', 'specialties'])->paginate(10);
    
            return ApiResponse::sendResponse(
                200,
                'Doctors list fetched successfully.',
                [
                    'data' => DoctorResource::collection($doctors),
                    'pagination' => [
                        'current_page' => $doctors->currentPage(),
                        'per_page' => $doctors->perPage(),
                        'total' => $doctors->total(),
                        'last_page' => $doctors->lastPage(),
                    ]
                ]
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
    
        $doctor = Doctor::with(['user', 
        'appointments' => function ($query) {
            $currentDate = now()->toDateString();
            $thresholdTime = now()->addHours(2)->addMinutes(30)->format('H:i:s'); // Add 30 minutes to give an enough time to get served if the client booked 30 minute before end_time of appointment  then +2 for egypt as it (UTC+2)
    
            $query->where(function ($q) use ($currentDate, $thresholdTime) {
                $q->whereDate('date', '>', $currentDate) // Future appointments
                  ->orWhere(function ($subQuery) use ($currentDate, $thresholdTime) {
                      $subQuery->whereDate('date', $currentDate) // Today's appointments
                               ->whereRaw("TIME(end_time) > ?", [$thresholdTime]); // Ensure 30 min buffer
                  });
            });
        },'specialties'])
            ->where('id', $id)->first();
        $data = new DoctorInfoResource($doctor);
    
        if ($doctor)
            return ApiResponse::sendResponse(
                200,
                'Doctor fetched successfully.',
                $data
            );
    
        return ApiResponse::sendResponse(500, 'Failed to fetch doctor data.', []);
    }
    
    public function filterBySpecialty(Request $request)
    {
        $specialtyId = $request->input('specialty_id');
        
        $doctors = Doctor::whereHas('specialties', function ($query) use ($specialtyId) {
            $query->where('specialties.id', $specialtyId);
        })->paginate(10);
    
        return ApiResponse::sendResponse(200, 'Doctors filtered by specialty successfully', [
            'data' => DoctorResource::collection($doctors),
            'pagination' => [
                'current_page' => $doctors->currentPage(),
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
                'last_page' => $doctors->lastPage(),
            ]
        ]);
    }
    
    public function searchByName(Request $request)
    {
        if ($request->has('name') && !empty($request->name)) {
            $doctors = Doctor::whereHas('user', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            })
            ->with('specialties', 'appointments')
            ->paginate(10);
    
            return ApiResponse::sendResponse(200, 'Doctors retrieved successfully', [
                'data' => DoctorResource::collection($doctors),
                'pagination' => [
                    'current_page' => $doctors->currentPage(),
                    'per_page' => $doctors->perPage(),
                    'total' => $doctors->total(),
                    'last_page' => $doctors->lastPage(),
                ]
            ]);
        }
    
        return ApiResponse::sendResponse(400, 'Name parameter is required', []);
    }
}

