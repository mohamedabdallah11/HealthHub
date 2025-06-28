<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Http\Resources\ClientBookingInfoResource;
use App\Helpers\ApiResponse;


class ClientController extends Controller
{
    private function getBookingsByStatus($userId, $status, $perPage = 10)
    {
        return Booking::with(['appointment'])
            ->where('user_id', $userId)
            ->where('status', $status)
            ->paginate($perPage);
    }

    public function getConfirmedBookings(Request $request)
    {
        $userId = auth()->user()->id; 
        $perPage = $request->input('per_page', 10);

        $bookings = $this->getBookingsByStatus($userId, 'confirmed', $perPage);
/* 
        if ($bookings->isEmpty()) {
            return ApiResponse::sendResponse(404, 'No confirmed bookings found', []);
        } */

        return ApiResponse::sendResponse(200, 'Confirmed bookings retrieved successfully', [
            'data' => ClientBookingInfoResource::collection($bookings),
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

    public function getServedBookings(Request $request)
    {
        $userId = auth()->user()->id;
        $perPage = $request->input('per_page', 10);

        $bookings = $this->getBookingsByStatus($userId, 'served', $perPage);

        return ApiResponse::sendResponse(200, 'Served bookings retrieved successfully', [
            'data' => ClientBookingInfoResource::collection($bookings),
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

    public function getPendingBookings(Request $request)
    {
        $userId = auth()->user()->id;
        $perPage = $request->input('per_page', 10);

        $bookings = $this->getBookingsByStatus($userId, 'pending', $perPage);

        return ApiResponse::sendResponse(200, 'Pending bookings retrieved successfully', [
            'data' => ClientBookingInfoResource::collection($bookings),
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
