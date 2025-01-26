<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\ClientResource;
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

        return ApiResponse::sendResponse(200, 'Booking marked as served successfully', new BookingResource($booking));
    }

    private function getBookingsByStatus($doctorId, $appointmentId, $status, $perPage = 10)
    {
        return Booking::with(['user', 'appointment'])
            ->where('doctor_id', $doctorId)
            ->where('appointment_id', $appointmentId)
            ->where('status', $status)
            ->paginate($perPage);
    }

    public function getConfirmedBookings(Request $request, $appointmentId)
    {
        $doctorId = auth()->user()->doctor->id;
        $perPage = $request->input('per_page', 10);

        $bookings = $this->getBookingsByStatus($doctorId, $appointmentId, 'confirmed', $perPage);

        if ($bookings->isEmpty()) {
            return ApiResponse::sendResponse(404, 'No confirmed bookings found', []);
        }
        
        return ApiResponse::sendResponse(200, 'Confirmed bookings retrieved successfully', [
            'data' => BookingResource::collection($bookings),
            'links' => [
                'first' => $bookings->url(1),
                'last' => $bookings->url($bookings->lastPage()),
                'prev' => $bookings->previousPageUrl(),
                'next' => $bookings->nextPageUrl()
            ],
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'total_pages' => $bookings->lastPage(),
                'total_items' => $bookings->total(),
                'items_per_page' => $perPage,
            ]
        ]);
    }

    public function getServedBookings(Request $request, $appointmentId)
    {
        $doctorId = auth()->user()->doctor->id;
        $perPage = $request->input('per_page', 1);

        $bookings = $this->getBookingsByStatus($doctorId, $appointmentId, 'served', $perPage);

        return ApiResponse::sendResponse(200, 'Served bookings retrieved successfully', [
            'data' => BookingResource::collection($bookings),
            'links' => [
                'first' => $bookings->url(1),
                'last' => $bookings->url($bookings->lastPage()),
                'prev' => $bookings->previousPageUrl(),
                'next' => $bookings->nextPageUrl()
            ],
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'total_pages' => $bookings->lastPage(),
                'total_items' => $bookings->total(),
                'items_per_page' => $perPage,
            ]
        ]);
    }
}

