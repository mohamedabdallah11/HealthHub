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

        $currentCapacity = $appointment->bookings()
            ->where('doctor_id', $doctorId)
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

}
