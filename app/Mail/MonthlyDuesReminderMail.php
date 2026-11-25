<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyDuesReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $memberName,
        public int $month,
        public int $year,
        public string $outstandingAmount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Monthly dues reminder: {$this->month}/{$this->year}",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.monthly-dues-reminder',
        );
    }
}
