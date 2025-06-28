<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\DoctorBookingMangmentResource;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Helpers\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


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
    $doctor = auth()->user()->doctor;
    $perPage = $request->input('per_page', 10);
    $currentPage = $request->input('page', 1);

    if (!$doctor->appointments()->find($appointmentId)) {
        return ApiResponse::sendResponse(404, 'Appointment not found', []);
    }

    $bookings = Booking::where('doctor_id', $doctorId)
        ->where('appointment_id', $appointmentId)
        ->where('status', 'confirmed')
        ->get()
        ->groupBy('appointment_id')
        ->map(fn ($bookingGroup) => new DoctorBookingMangmentResource($bookingGroup->first(),'confirmed'))
        ->values(); 

    if ($bookings->isEmpty()) {
        return ApiResponse::sendResponse(404, 'No confirmed bookings found', []);
    }

    $paginatedBookings = new LengthAwarePaginator(
        $bookings->forPage($currentPage, $perPage), 
        $bookings->count(), 
        $perPage, 
        $currentPage, 
        ['path' => url()->current()] 
    );

    return ApiResponse::sendResponse(200, 'Confirmed bookings retrieved successfully', [
        'data' => $paginatedBookings->items(),
        'links' => [
            'first' => $paginatedBookings->url(1),
            'last' => $paginatedBookings->url($paginatedBookings->lastPage()),
            'prev' => $paginatedBookings->previousPageUrl(),
            'next' => $paginatedBookings->nextPageUrl(),
        ],
        'meta' => [
            'current_page' => $paginatedBookings->currentPage(),
            'total_pages' => $paginatedBookings->lastPage(),
            'total_items' => $paginatedBookings->total(),
            'items_per_page' => $perPage,
        ]
    ]);
}
public function getServedBookings(Request $request, $appointmentId)
{   
    $doctor = auth()->user()->doctor;
    $perPage = $request->input('per_page', 10);
    $currentPage = $request->input('page', 1);

    if (!$doctor->appointments()->find($appointmentId)) {
        return ApiResponse::sendResponse(404, 'Appointment not found', []);
    }

    $doctorId = auth()->user()->doctor->id;

    $bookings = Booking::where('doctor_id', $doctorId)
        ->where('appointment_id', $appointmentId)
        ->where('status', 'served')
        ->get()
        ->groupBy('appointment_id')
        ->map(fn ($bookingGroup) => new DoctorBookingMangmentResource($bookingGroup->first(),'served'))
        ->values(); 

    if ($bookings->isEmpty()) {
        return ApiResponse::sendResponse(404, 'No served bookings found', []);
    }

    $paginatedBookings = new LengthAwarePaginator(
        $bookings->forPage($currentPage, $perPage), 
        $bookings->count(), 
        $perPage, 
        $currentPage, 
        ['path' => url()->current()]
    );

    return ApiResponse::sendResponse(200, 'Served bookings retrieved successfully', [
        'data' => $paginatedBookings->items(),
        'links' => [
            'first' => $paginatedBookings->url(1),
            'last' => $paginatedBookings->url($paginatedBookings->lastPage()),
            'prev' => $paginatedBookings->previousPageUrl(),
            'next' => $paginatedBookings->nextPageUrl(),
        ],
        'meta' => [
            'current_page' => $paginatedBookings->currentPage(),
            'total_pages' => $paginatedBookings->lastPage(),
            'total_items' => $paginatedBookings->total(),
            'items_per_page' => $perPage,
        ]
    ]);
}
}

