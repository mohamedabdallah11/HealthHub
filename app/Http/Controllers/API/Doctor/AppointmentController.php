<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Helpers\ApiResponse;
use Carbon\Carbon;

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
            return ApiResponse::sendResponse(404, 'There are no appointments', []);
        }

        return ApiResponse::sendResponse(200, 'Appointments retrieved successfully', AppointmentResource::collection($appointments));
    }

    public function store(AppointmentRequest $request)
    {
        $doctor_id = auth()->user()->doctor->id;
        $doctor_name = auth()->user()->name;
        $date = $request->date;
        $start_time = Carbon::parse($request->start_time);
        $end_time = Carbon::parse($request->end_time);
        $session_duration = $request->session_duration;
        $is_available = $request->is_available;
        $max_patients = $request->max_patients;

        if (($end_time->diffInMinutes($start_time) % $session_duration) !== 0) {
            return ApiResponse::sendResponse(400, 'Session duration must evenly divide the appointment time range', []);
        }

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
            'session_duration' => Carbon::createFromTimestampUTC($session_duration * 60)->format('H:i'),
            'is_available' => $is_available,
            'max_patients' => $max_patients
        ]);

        return ApiResponse::sendResponse(200, 'Appointment created successfully', new AppointmentResource($appointment));
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

        if ($appointment->doctor_id !== auth()->user()->doctor->id) {
            return ApiResponse::sendResponse(403, 'Forbidden', []);
        }

        $date = $request->date;
        $start_time = Carbon::parse($request->start_time);
        $end_time = Carbon::parse($request->end_time);
        $session_duration = $request->session_duration;

        if (($end_time->diffInMinutes($start_time) % $session_duration) !== 0) {
            return ApiResponse::sendResponse(400, 'Session duration must evenly divide the appointment time range', []);
        }

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
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'session_duration' => Carbon::createFromTimestampUTC($session_duration * 60)->format('H:i'),
            'is_available' => $request->is_available,
            'max_patients' => $request->max_patients
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
    public function getAvailableSlots($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
    
        if (!$appointment) {
            return ApiResponse::sendResponse(404, 'Appointment not found', []);
        }
    
        $slots = [];
        $startTime = Carbon::parse($appointment->start_time);
        $endTime = Carbon::parse($appointment->end_time);
    
        $sessionDuration = Carbon::parse($appointment->session_duration)->hour * 60 
                         + Carbon::parse($appointment->session_duration)->minute;
    
        if ($sessionDuration <= 0) {
            return ApiResponse::sendResponse(400, 'Invalid session duration', []);
        }
    
        $currentTime = $startTime->copy();
    
        while ($currentTime->lessThan($endTime)) {
            $slotTime = $currentTime->format('H:i');
    
            $isBooked = Booking::where('appointment_id', $appointmentId)
                               ->where('status', 'confirmed')
                               ->where('slot_start_time', $slotTime) 
                               ->exists();
    
            $slots[] = [
                'time' => $slotTime,
                'available' => !$isBooked
            ];
    
            $currentTime->addMinutes($sessionDuration);
        }
    
        return ApiResponse::sendResponse(200, 'Available slots retrieved successfully', $slots);
    }
}