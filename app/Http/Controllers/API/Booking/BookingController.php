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
use App\Mail\BookingConfirmed;
use Illuminate\Support\Facades\Mail;
class BookingController extends Controller
{
    public function bookAppointment(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $appointmentId = $request->input('appointment_id');
        $slotStartTime = Carbon::parse($request->input('slot_start_time'))->format('H:i:s');
        $userId = auth()->user()->id;

        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            return ApiResponse::sendResponse(404, 'Appointment not found', []);
        }
        if (!$appointment->is_available) {
            return ApiResponse::sendResponse(400, 'This appointment is not available', []);
        }

        $sessionDuration = Carbon::parse($appointment->session_duration)->hour * 60
            + Carbon::parse($appointment->session_duration)->minute;

        if ($sessionDuration <= 0) {
            return ApiResponse::sendResponse(400, 'Invalid session duration', []);
        }

        $slotEndTime = Carbon::parse($slotStartTime)->addMinutes($sessionDuration)->format('H:i:s');
        $appointmentStartTime = Carbon::parse($appointment->start_time)->format('H:i:s');
        $appointmentEndTime = Carbon::parse($appointment->end_time)->format('H:i:s');

        if ($slotStartTime < $appointmentStartTime || $slotEndTime > $appointmentEndTime) {
            return ApiResponse::sendResponse(400, 'Selected slot is outside appointment hours', [
                'slot_start_time' => $slotStartTime,
                'slot_end_time' => $slotEndTime,
                'appointment_start_time' => $appointmentStartTime,
                'appointment_end_time' => $appointmentEndTime,
            ]);
        }

        $confirmedBookingsCount = Booking::where('appointment_id', $appointmentId)
            ->where('status', 'confirmed')
            ->count();

        if ($confirmedBookingsCount >= $appointment->max_patients) {
            return ApiResponse::sendResponse(400, 'This appointment is fully booked', []);
        }

        $existingBooking = Booking::where('appointment_id', $appointmentId)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($slotStartTime, $slotEndTime) {
                $query->where('slot_start_time', '<', $slotEndTime)
                    ->where('slot_end_time', '>', $slotStartTime);
            })
            ->exists();

        if ($existingBooking) {
            return ApiResponse::sendResponse(400, 'This slot is already booked', []);
        }

        DB::beginTransaction();

        try {
            $booking = new Booking();
            $booking->user_id = $userId;
            $booking->appointment_id = $appointmentId;
            $booking->doctor_id = $doctorId;
            $booking->slot_start_time = $slotStartTime;
            $booking->slot_end_time = $slotEndTime;
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
        if ($booking->user_id !== auth()->id()) {
    return ApiResponse::sendResponse(403, 'You are not authorized to confirm this booking', []);
}

        if (!$booking) {
            return ApiResponse::sendResponse(404, 'Booking not found', []);
        }

        if ($booking->status !== 'pending') {
            return ApiResponse::sendResponse(400, 'Booking is not pending', []);
        }

        $appointment = $booking->appointment;

        if (!$appointment) {
            return ApiResponse::sendResponse(404, 'Appointment not found', []);
        }

        $isOverlapping = Booking::where('appointment_id', $booking->appointment_id)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($booking) {
                $query->where(function ($sub) use ($booking) {
                    $sub->where('slot_start_time', '<', $booking->slot_end_time)
                        ->where('slot_end_time', '>', $booking->slot_start_time);
                });
            })
            ->exists();

        if ($isOverlapping) {
            return ApiResponse::sendResponse(400, 'Cannot confirm booking, time slot is no longer available', []);
        }

        $confirmedBookingsCount = Booking::where('appointment_id', $booking->appointment_id)
            ->where('status', 'confirmed')
            ->count();

        if ($confirmedBookingsCount >= $appointment->max_patients) {
            return ApiResponse::sendResponse(400, 'Cannot confirm booking, maximum capacity reached', []);
        }
        /* $googleMeetService = new GoogleMeetService();
           $meetLink = $googleMeetService->createMeeting($booking);


           Mail::to($booking->user->email)->send(new BookingConfirmed($booking, $meetLink));
        */
        Mail::to($booking->user->email)->send(new BookingConfirmed($booking));
        $booking->google_meet_link = "https://meet.google.com/vor-dodq-sda";

        $booking->status = 'confirmed';

        $booking->save();

        return ApiResponse::sendResponse(200, 'Booking confirmed and Email sent successfully', new BookingResource($booking));
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
