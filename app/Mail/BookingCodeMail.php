<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCodeMail extends Mailable
{
    use Queueable, SerializesModels;


    public Booking $booking; //biến chứa dữ liệu booking

    public function __construct(Booking $booking) //chèn booking vào mail
    {
        $this->booking = $booking;
    }

    /**
     * Build nội dung email
     */
    public function build()
    {
        return $this->subject('Barbery - Mã tra cứu lịch hẹn')
            ->view('emails.booking_code');
    }
}
