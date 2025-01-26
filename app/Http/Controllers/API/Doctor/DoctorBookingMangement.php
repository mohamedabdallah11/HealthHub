<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Helpers\ApiResponse;


class DoctorBookingMangement extends Controller
{
    public function markBookingAsServed($bookingId)
    {
        $doctorId = auth()->user()->doctor->id;

        $booking = Booking::where('id', $bookingId)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$booking) {
            return ApiResponse::sendResponse(404, 'Booking not found', []);
        }

        if ($booking->status !== 'confirmed') {
            return ApiResponse::sendResponse(400, 'Only confirmed bookings can be marked as served', []);
        }

        $booking->status = 'served';  
        $booking->save();

        return ApiResponse::sendResponse(200, 'Booking marked as served successfully', $booking);
    }

}
