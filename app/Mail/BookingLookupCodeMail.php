<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingLookupCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Booking vừa tạo
     */
    public Booking $booking;

    /**
     * Inject booking vào mail
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Build nội dung email
     */
    public function build()
    {
        return $this->subject('Barbery - Mã tra cứu lịch hẹn')
            ->view('emails.booking_lookup_code');
    }
}
