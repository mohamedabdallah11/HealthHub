<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Helpers\ApiResponse;



class AppointmentController extends Controller
{
    public function show()
    {     
        $doctor = auth()->user()->doctor;   
        if (!$doctor) {
            return ApiResponse::sendResponse(404, 'Doctor not found', []);
        }
        $appointments = $doctor->appointments;
        if ($appointments->isEmpty()) {
            return ApiResponse::sendResponse(404, 'there are no appointments  ', []);
        }

        return ApiResponse::sendResponse(200, 'Appointments retrieved successfully', AppointmentResource::collection($appointments));
    }

    
    public function store(AppointmentRequest $request)
    {
    
        $doctor_id = auth()->user()->doctor->id; 
        $doctor_name = auth()->user()->name;
        $date = $request->date;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $session_duration = $request->session_duration;
        $is_available = $request->is_available;
    
        $existingAppointment = Appointment::where('doctor_id', $doctor_id)
            ->where('date', $date)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                      ->orWhereBetween('end_time', [$start_time, $end_time]);
            })
            ->exists();
    
        if ($existingAppointment) {
            return ApiResponse::sendResponse(400, 'Appointment already exists', []);
        }
    
        $appointment = Appointment::create([
            'doctor_id' => $doctor_id,
            'doctor_name' => $doctor_name,
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'session_duration' => $session_duration,
            'is_available' => $is_available
        ]);
    
        $data = new AppointmentResource($appointment);
    
        return ApiResponse::sendResponse(200, 'Appointment created successfully', $data);
    } 
    

    public function update(AppointmentRequest $request, $appointmentId)
{
 
    $doctor = auth()->user()->doctor;
    if (!$doctor) {
        return ApiResponse::sendResponse(401, 'Unauthorized', []);
    }
    $appointment = $doctor->appointments()->find($appointmentId);
    if (!$appointment) {
        return ApiResponse::sendResponse(404, 'Appointment not found', []);
    }

    if ($appointment->doctor_id !== auth()->user()->id) {
        return ApiResponse::sendResponse(403, 'Forbidden', []);
    }

    $date = $request->date;
    $start_time = $request->start_time;
    $end_time = $request->end_time;

    $existingAppointment = Appointment::where('doctor_id', $appointment->doctor_id)
        ->where('date', $date)
        ->where(function ($query) use ($start_time, $end_time) {
            $query->whereBetween('start_time', [$start_time, $end_time])
                  ->orWhereBetween('end_time', [$start_time, $end_time]);
        })
        ->where('id', '!=', $appointmentId)
        ->exists();

    if ($existingAppointment) {
        return ApiResponse::sendResponse(400, 'Appointment already exists at this time', []);
    }

    $appointment->update([
        'date' => $request->date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'session_duration' => $request->session_duration,
        'is_available' => $request->is_available,
    ]);

    return ApiResponse::sendResponse(200, 'Appointment updated successfully', new AppointmentResource($appointment));
    }
    

    public function destroy($appointmentId)
    { 
        $doctor = auth()->user()->doctor;
        if (!$doctor) {
            return ApiResponse::sendResponse(401, 'Unauthorized', []);
        }
        $appointment = $doctor->appointments()->find($appointmentId);
        if (!$appointment) {
            return ApiResponse::sendResponse(404, 'Appointment not found', []);
        }

        $appointment->delete();

        return ApiResponse::sendResponse(200, 'Appointment deleted successfully', []);
        
        }
    public function deactivate($appointmentId)
    {
     
        $doctor = auth()->user()->doctor;
        if (!$doctor) {
            return ApiResponse::sendResponse(401, 'Unauthorized', []);
        }
        $appointment = $doctor->appointments()->find($appointmentId);

        if (!$appointment) {
            return ApiResponse::sendResponse(404, 'Appointment not found', []);
        }
    
        $appointment->update(['is_available' => false]);
    
        return ApiResponse::sendResponse(200, 'Appointment deactivated successfully', []);
    }
}
