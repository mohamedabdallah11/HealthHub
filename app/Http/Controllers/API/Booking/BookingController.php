<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;
use App\Models\Booking;
use App\Http\Resources\BookingResource;
use App\Models\Doctor;
use Carbon\Carbon;
class BookingController extends Controller
{
    public function bookAppointment(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $appointmentId = $request->input('appointment_id');
        $userId = auth()->user()->id;

        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            return ApiResponse::sendResponse(404, 'Appointment not found', []);
        }
        if (!$appointment->is_available) {
            return ApiResponse::sendResponse(400, 'This appointment is not available', []);
        }
        $currentCapacity = $appointment->bookings()
            ->where('doctor_id', $doctorId )
            ->count();

        if ($currentCapacity >= $appointment->max_patients) {
            return ApiResponse::sendResponse(400, 'This appointment is fully booked', []);
        }
        $doctorId = $request->input('doctor_id');
        $doctor = Doctor::find($doctorId);

        if (!$doctor) {
            return ApiResponse::sendResponse(404, 'Doctor not found', []);
        }
        $existingBooking = Booking::where('user_id', $userId)
            ->where('appointment_id', $appointmentId)
            ->first();

        if ($existingBooking) {
            return ApiResponse::sendResponse(400, 'You have already booked this appointment', []);
        }
        DB::beginTransaction();

        try {
            $booking = new Booking();
            $booking->user_id = $userId;
            $booking->appointment_id = $appointmentId;
            $booking->doctor_id = $doctorId;
            $booking->status = 'pending';
            $booking->save();

            DB::commit();

            return ApiResponse::sendResponse(200, 'Booking successful', new BookingResource($booking));

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, 'Booking failed', $e->getMessage());
        }
    }
    public function confirmBooking($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return ApiResponse::sendResponse(404, 'Booking not found', []);
        }
        if ($booking->status !== 'pending') {
            return ApiResponse::sendResponse(400, 'Booking is not pending', []);}
        $booking->status = 'confirmed';
        $booking->save();

        return ApiResponse::sendResponse(200, 'Booking confirmed successfully', new BookingResource($booking));
    }

    public function cancelBooking($id)
    {
        DB::beginTransaction();
        
        try {
            $booking = Booking::find($id);
    
            if (!$booking) {
                return ApiResponse::sendResponse(404, 'Booking not found', []);
            }
    
            if ($booking->user_id !== auth()->id()) {
                return ApiResponse::sendResponse(403, 'Unauthorized to cancel this booking', []);
            }
    
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return ApiResponse::sendResponse(400, 'Only pending or confirmed bookings can be canceled', []);
            }
            
            $appointmentDate = Carbon::parse($booking->appointment->date)->setTimezone('UTC');
            $startTime = Carbon::parse($booking->appointment->start_time)->setTimezone('UTC');
            
            $now = Carbon::now('UTC');
    
            if ($now->toDateString() === $appointmentDate->toDateString()) {
                $cancellationDeadline = $startTime->subHours(2);
                if ($now->lessThan($cancellationDeadline)) {
                    return ApiResponse::sendResponse(400, 'Cancellation is not allowed within 2 hours of the appointment', $now);
                }
            }
    
            if ($now->greaterThan($appointmentDate)) {
                return ApiResponse::sendResponse(400, 'Cancellation is not allowed after the appointment date', []);
            }
    
            $booking->delete();
    
            DB::commit();
    
            return ApiResponse::sendResponse(200, 'Booking canceled successfully', []);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, 'Failed to cancel booking', $e->getMessage());
        }
    }
}
