<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $judul,
        public string $pesan,
        public ?string $ctaLabel = null,
        public ?string $ctaUrl = null,
    ) {}

    public function build()
    {
        return $this->subject($this->judul)->view('emails.notification');
    }
}