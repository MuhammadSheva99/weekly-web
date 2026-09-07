<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otpCode, public string $userName)
    {
    }

    public function build()
    {
        return $this->subject('Kode OTP Reset Password')
            ->view('emails.otp');
    }
}