<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RepairOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_data;
    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data, $pdf)
    {
        $this->mail_data = $mail_data;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('ส่งข้อมูลใบสั่งซ่อม' . $this->mail_data['license_plate'])->markdown('emails.repair-order-mail')
            ->with('mail_data', $this->mail_data)
            ->attachData($this->pdf->output(), 'ใบสั่งซ่อม.pdf');
    }
}
