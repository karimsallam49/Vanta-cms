<?php

namespace App\Mail;

use App\Models\TestRideRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestRideStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TestRideRequest $ride) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your VANTA MOTO test ride — '.$this->ride->status->label(),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.test-ride-status');
    }
}
