<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $meetLink;

    public function __construct($booking, $meetLink="https://meet.google.com/vor-dodq-sda")
    {
        $this->booking = $booking;
        $this->meetLink = $meetLink;
    }

    public function build()
    {
        return $this->subject('تأكيد حجز الجلسة')
                    ->view('emails.booking_confirmed');
    }
}
