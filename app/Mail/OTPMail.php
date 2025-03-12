<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $otp)
    {
    }

    public function build(): static
    {
        return $this->subject('Your OTP Code')
                    ->view('emails.otp')
                    ->with(['otp' => $this->otp]);
    }
    /**
     * Get the message envelope.
     */
}
